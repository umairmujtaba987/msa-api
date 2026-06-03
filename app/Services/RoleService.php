<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

final class RoleService
{
    public function all(): Collection
    {
        return Role::query()->orderBy('name')->get();
    }

    public function create(string $name): Role
    {
        return Role::query()->create(['name' => $name]);
    }

    public function update(Role $role, string $name): Role
    {
        $role->update(['name' => $name]);

        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
