<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IMemoryService;
use App\AssistantRAR\Models\AssistantMemory;

class MemoryService implements IMemoryService
{
    public function get(int $userId, string $key): ?string
    {
        return AssistantMemory::where('user_id', $userId)
            ->where('key', $key)
            ->value('value');
    }

    public function set(int $userId, string $key, string $value, ?string $category = null): void
    {
        AssistantMemory::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value, 'category' => $category],
        );
    }

    public function delete(int $userId, string $key): void
    {
        AssistantMemory::where('user_id', $userId)->where('key', $key)->delete();
    }

    public function getAll(int $userId): array
    {
        return AssistantMemory::where('user_id', $userId)->get()->toArray();
    }

    public function getByCategory(int $userId, string $category): array
    {
        return AssistantMemory::where('user_id', $userId)
            ->where('category', $category)
            ->get()
            ->toArray();
    }
}
