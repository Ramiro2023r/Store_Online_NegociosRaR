<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReviewService;

class ReviewRejectTool extends BaseTool
{
    public function name(): string { return 'review.reject'; }
    public function description(): string { return 'Rechazar (desaprobar) una reseña.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID de la reseña'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(ReviewService::class)->reject($arguments['id']);
        return $this->success('Reseña rechazada.', [], ['resource_type' => 'review', 'resource_id' => $arguments['id']]);
    }
}
