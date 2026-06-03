<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->attemptLogin(
            $request->validated('email'),
            $request->validated('password'),
        );

        if ($result === null) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'token' => $result['token'],
            'user' => (new UserResource($result['user']))->resolve($request),
            'roles' => $result['roles'],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        return ApiResponse::success(
            (new UserResource($request->user()->load('roles')))->resolve($request),
            'Profile fetched successfully.',
        );
    }

    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $result = $this->authService->updateProfile($request->user(), $request->validated());

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => (new UserResource($result['user']))->resolve($request),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logoutCurrentToken($request->user());

        return response()->json(['message' => 'Logged out']);
    }
}
