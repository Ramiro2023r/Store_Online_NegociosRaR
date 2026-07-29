<?php

namespace App\AssistantRAR\Tools;

use App\Services\ReportService;

class ReportInventoryTool extends BaseTool
{
    public function name(): string
    {
        return 'report.inventory';
    }

    public function description(): string
    {
        return 'Generar reporte del estado actual del inventario (totales, stock bajo, agotados).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
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
        $report = $service->inventoryReport();

        return $this->success('Reporte de inventario generado.', ['report' => $report]);
    }
}
