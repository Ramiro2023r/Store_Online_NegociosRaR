<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReviewService;

class ReviewSummaryTool extends BaseTool
{
    public function name(): string { return 'review.summary'; }
    public function description(): string { return 'Obtener resumen de reseñas (totales, aprobadas, pendientes).'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Resumen de reseñas.', ['summary' => app(ReviewService::class)->summary()]);
    }
}
