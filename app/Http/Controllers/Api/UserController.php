<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\IndexUserRequest;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {
    }

    public function index(IndexUserRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $search = (string) ($validated['search'] ?? '');
        $perPage = (int) ($validated['per_page'] ?? 10);

        $paginator = $this->userService->paginateWithRoles($search, $perPage);
        $paginator->through(fn (User $user) => (new UserResource($user))->resolve($request));

        return response()->json($paginator);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        return response()->json((new UserResource($user))->resolve($request), 201);
    }

    public function show(Request $request, User $user): JsonResponse
    {
        $user->loadMissing('roles');

        return response()->json((new UserResource($user))->resolve($request));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $user = $this->userService->update($user, $request->validated());

        return response()->json((new UserResource($user))->resolve($request));
    }

    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);

        return response()->json(['message' => 'User deleted']);
    }
}
