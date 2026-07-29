<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReviewService;

class ReviewDeleteTool extends BaseTool
{
    public function name(): string { return 'review.delete'; }
    public function description(): string { return 'Eliminar una reseña permanentemente.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID de la reseña'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(ReviewService::class)->delete($arguments['id']);
        return $this->success('Reseña eliminada.', [], ['resource_type' => 'review', 'resource_id' => $arguments['id']]);
    }
}
