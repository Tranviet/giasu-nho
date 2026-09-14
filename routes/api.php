<?php

use App\Http\Controllers\Api\V1\AiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ChildController;
use App\Http\Controllers\Api\V1\CurriculumController;
use App\Http\Controllers\Api\V1\ProgressController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // 1. Auth routes (Public)
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);

        // Auth routes (Protected)
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    // 2. Curriculum routes (Public)
    Route::get('subjects', [CurriculumController::class, 'subjects']);
    Route::get('topics', [CurriculumController::class, 'topics']);
    Route::get('topics/{topic}', [CurriculumController::class, 'topicDetail']);
    Route::get('topics/{topic}/exercises', [CurriculumController::class, 'exercises']);

    // 3. Protected routes (Authenticated parents)
    Route::middleware('auth:sanctum')->group(function () {
        // Children CRUD
        Route::apiResource('children', ChildController::class);

        // Progress
        Route::get('children/{child}/progress', [ProgressController::class, 'show']);

        // Submit exercise
        Route::post('exercises/{exercise}/submit', [CurriculumController::class, 'submitExercise']);

        // AI Endpoints (with anti-spam rate limiting: 5 requests per minute)
        Route::middleware('throttle:5,1')->prefix('ai')->group(function () {
            Route::post('homework-check', [AiController::class, 'checkHomework']);
            Route::post('qa', [AiController::class, 'askQuestion']);
        });
    });
});
