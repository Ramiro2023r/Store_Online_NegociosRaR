<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function search(array $filters = [], int $perPage = 20): array
    {
        $query = User::query();

        if (!empty($filters['query'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'ilike', '%'.$filters['query'].'%')
                  ->orWhere('email', 'ilike', '%'.$filters['query'].'%');
            });
        }
        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }
        if (isset($filters['active'])) {
            $query->where('active', $filters['active']);
        }

        return $query->latest()->paginate($perPage)->toArray();
    }

    public function find(int $id): ?User
    {
        return User::find($id);
    }

    public function createWorker(array $data): User
    {
        $data['password'] = Hash::make($data['password'] ?? Str::random(16));
        $data['role'] = 'trabajador';
        $data['active'] = true;
        return User::create($data);
    }

    public function update(int $id, array $data): User
    {
        $user = User::findOrFail($id);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $user->update($data);
        return $user->fresh();
    }

    public function changeRole(int $id, string $role): User
    {
        $user = User::findOrFail($id);
        $user->update(['role' => $role]);
        return $user->fresh();
    }

    public function block(int $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['active' => false]);
        return $user->fresh();
    }

    public function unblock(int $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['active' => true]);
        return $user->fresh();
    }

    public function generatePasswordResetLink(int $id): string
    {
        $user = User::findOrFail($id);
        $token = app('auth.password.broker')->createToken($user);
        return route('password.reset', ['token' => $token, 'email' => $user->email]);
    }
}
