<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $roles = $this->roleService->all()->map(
            fn (Role $role) => (new RoleResource($role))->resolve($request)
        );

        return response()->json($roles->values()->all());
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated('name'));

        return response()->json((new RoleResource($role))->resolve($request), 201);
    }

    public function show(Request $request, Role $role): JsonResponse
    {
        return response()->json((new RoleResource($role))->resolve($request));
    }

    public function update(UpdateRoleRequest $request, Role $role): JsonResponse
    {
        $role = $this->roleService->update($role, $request->validated('name'));

        return response()->json((new RoleResource($role))->resolve($request));
    }

    public function destroy(Role $role): JsonResponse
    {
        $this->roleService->delete($role);

        return response()->json(['message' => 'Role deleted']);
    }
}
