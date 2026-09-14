<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\ChildResource;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\QuotaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new parent user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Auto assign free plan subscription
        $freePlan = Plan::where('code', 'free')->first();
        if ($freePlan) {
            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $freePlan->id,
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addMonth(),
            ]);
        }

        $token = $user->createToken('parent-token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng ký tài khoản thành công.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Authenticate parent and issue Sanctum token.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không chính xác.'],
            ]);
        }

        $token = $user->createToken('parent-token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Logout and revoke current access token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công.',
        ]);
    }

    /**
     * Get authenticated parent profile, children, and quota status.
     */
    public function me(Request $request, QuotaService $quotaService): JsonResponse
    {
        $user = $request->user()->load('children');

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'children' => ChildResource::collection($user->children),
            'subscription' => $quotaService->getQuotaSummary($user),
        ]);
    }
}
