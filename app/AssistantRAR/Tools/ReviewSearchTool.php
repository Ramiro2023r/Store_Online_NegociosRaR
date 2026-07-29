<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReviewService;

class ReviewSearchTool extends BaseTool
{
    public function name(): string { return 'review.search'; }
    public function description(): string { return 'Buscar reseñas por estado (pending/approved).'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'status' => ['type' => 'string', 'enum' => ['pending', 'approved'], 'description' => 'Estado'],
        ]];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Reseñas obtenidas.', ['reviews' => app(ReviewService::class)->search($arguments['status'] ?? null)]);
    }
}
