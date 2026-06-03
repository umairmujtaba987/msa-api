<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

final class AuthService
{
    /**
     * @return array{token: string, user: User, roles: \Illuminate\Support\Collection<int, string>}|null
     */
    public function attemptLogin(string $email, string $password): ?array
    {
        $user = User::query()->with('roles')->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return null;
        }

        $token = $user->createToken('api_token')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
            'roles' => $user->getRoleNames(),
        ];
    }

    /**
     * @param  array{name: string, email: string, current_password?: string|null, new_password?: string|null, new_password_confirmation?: string|null}  $data
     * @return array{user: User}|array{error: string}
     */
    public function updateProfile(User $user, array $data): array
    {
        if (! empty($data['new_password'])) {
            if (empty($data['current_password']) || ! Hash::check($data['current_password'], $user->password)) {
                return ['error' => 'Current password is incorrect.'];
            }

            $user->password = Hash::make($data['new_password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->save();

        return ['user' => $user->load('roles')];
    }

    public function logoutCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
