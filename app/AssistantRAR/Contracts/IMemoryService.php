<?php

namespace App\AssistantRAR\Contracts;

interface IMemoryService
{
    public function get(int $userId, string $key): ?string;
    public function set(int $userId, string $key, string $value, ?string $category = null): void;
    public function delete(int $userId, string $key): void;
    public function getAll(int $userId): array;
    public function getByCategory(int $userId, string $category): array;
}
