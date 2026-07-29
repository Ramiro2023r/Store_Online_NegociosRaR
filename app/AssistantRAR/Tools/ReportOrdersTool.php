<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReportService;

class ReportOrdersTool extends BaseTool
{
    public function name(): string
    {
        return 'report.orders';
    }

    public function description(): string
    {
        return 'Generar reporte de pedidos agrupados por estado en un período.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
            ],
        ];
    }

    public function roles(): array
    {
        return ['admin'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        $service = app(ReportService::class);
        $report = $service->orderReport($arguments['date_from'] ?? null, $arguments['date_to'] ?? null);

        return $this->success('Reporte de pedidos generado.', ['report' => $report]);
    }
}
