<?php

namespace App\AssistantRAR\Contracts;

interface IContextService
{
    public function build(int $userId, ?string $currentRoute = null, ?int $resourceId = null, ?int $conversationId = null): array;
    public function getAvailableTools(int $userId): array;
}
