<?php

namespace App\Services;

use App\Models\LoyaltyTransaction;
use App\Models\User;

class LoyaltyService
{
    public function getBalance(int $userId): int
    {
        return (int) User::where('id', $userId)->value('loyalty_points');
    }

    public function getMovements(int $userId, int $limit = 20): array
    {
        return LoyaltyTransaction::where('user_id', $userId)
            ->latest()->limit($limit)->get()->toArray();
    }

    public function adjustBalance(int $userId, int $points, string $reason): User
    {
        $user = User::findOrFail($userId);
        $user->increment('loyalty_points', $points);
        LoyaltyTransaction::create([
            'user_id' => $userId,
            'type' => $points > 0 ? 'earned' : 'spent',
            'points' => $points,
            'description' => $reason,
        ]);
        return $user->fresh();
    }
}
