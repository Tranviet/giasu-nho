<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('child.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('child.dashboard'))
                ->with('success', 'Đăng nhập thành công! Chúc bé có buổi học thật vui.');
        }

        throw ValidationException::withMessages([
            'email' => ['Email hoặc mật khẩu không chính xác. Vui lòng kiểm tra lại.'],
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegistrationForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('child.dashboard');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'child_name' => ['nullable', 'string', 'max:100'],
            'child_grade' => ['nullable', 'integer', 'min:1', 'max:5'],
        ], [
            'name.required' => 'Vui lòng nhập họ tên phụ huynh.',
            'email.required' => 'Vui lòng nhập email.',
            'email.unique' => 'Email này đã được sử dụng. Bạn có thể đăng nhập.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
        ]);

        // 1. Create parent user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // 2. Assign Free plan subscription
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

        // 3. Create initial child profile
        $childName = !empty($validated['child_name']) ? trim($validated['child_name']) : 'Bé của ' . $validated['name'];
        $childGrade = !empty($validated['child_grade']) ? (int) $validated['child_grade'] : 2;

        Child::create([
            'user_id' => $user->id,
            'name' => $childName,
            'grade' => $childGrade,
            'avatar' => 'tiger',
        ]);

        // 4. Log in immediately
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('child.dashboard')
            ->with('success', 'Chào mừng gia đình đã tham gia Gia Sư Nhỏ! Hồ sơ học của bé đã sẵn sàng.');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('info', 'Bạn đã đăng xuất thành công.');
    }
}
