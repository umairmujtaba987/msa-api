<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        $users = User::with('roles')->paginate(10);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$request->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized. Only superadmin can create users.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
            'role' => ['required', 'exists:roles,name']
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return response()->json(['message' => 'User created successfully', 'user' => $user->load('roles')], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json($user->load('roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        if (!$request->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized. Only superadmin can update users.');
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password' => ['nullable', 'string', Password::defaults()],
            'role' => ['sometimes', 'required', 'exists:roles,name']
        ]);

        if (array_key_exists('name', $validated)) {
            $user->name = $validated['name'];
        }
        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        if (array_key_exists('role', $validated)) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json(['message' => 'User updated successfully', 'user' => $user->load('roles')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (!$request->user()->hasRole('superadmin')) {
            abort(403, 'Unauthorized. Only superadmin can delete users.');
        }

        if ($request->user()->id === $user->id) {
            abort(400, 'You cannot delete yourself.');
        }

        // Prevent deleting the last superadmin
        if ($user->hasRole('superadmin')) {
            $superadminCount = User::role('superadmin')->count();
            if ($superadminCount <= 1) {
                abort(400, 'Cannot delete the only superadmin.');
            }
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
