<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChildRequest;
use App\Http\Resources\ChildResource;
use App\Models\Child;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Gate;

class ChildController extends Controller
{
    /**
     * Display a listing of children for the authenticated parent.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $children = $request->user()->children()->latest()->get();

        return ChildResource::collection($children);
    }

    /**
     * Store a newly created child profile.
     */
    public function store(ChildRequest $request): JsonResponse
    {
        $child = $request->user()->children()->create($request->validated());

        return response()->json([
            'message' => 'Tạo hồ sơ bé thành công.',
            'child' => new ChildResource($child),
        ], 201);
    }

    /**
     * Display the specified child profile.
     */
    public function show(Request $request, Child $child): ChildResource
    {
        Gate::authorize('view', $child);

        return new ChildResource($child);
    }

    /**
     * Update the specified child profile.
     */
    public function update(ChildRequest $request, Child $child): JsonResponse
    {
        Gate::authorize('update', $child);

        $child->update($request->validated());

        return response()->json([
            'message' => 'Cập nhật hồ sơ bé thành công.',
            'child' => new ChildResource($child),
        ]);
    }

    /**
     * Remove the specified child profile.
     */
    public function destroy(Request $request, Child $child): JsonResponse
    {
        Gate::authorize('delete', $child);

        $child->delete();

        return response()->json([
            'message' => 'Đã xóa hồ sơ bé thành công.',
        ]);
    }
}
