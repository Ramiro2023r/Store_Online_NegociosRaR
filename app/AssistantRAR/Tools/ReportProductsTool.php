<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReportService;

class ReportProductsTool extends BaseTool
{
    public function name(): string
    {
        return 'report.products';
    }

    public function description(): string
    {
        return 'Generar reporte de productos más vendidos en un período.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
                'limit' => ['type' => 'integer', 'description' => 'Máximo de productos'],
            ],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        $service = app(ReportService::class);
        $products = $service->topProducts($arguments['date_from'] ?? null, $arguments['date_to'] ?? null, $arguments['limit'] ?? 20);

        return $this->success('Reporte de productos generado.', ['products' => $products]);
    }
}
