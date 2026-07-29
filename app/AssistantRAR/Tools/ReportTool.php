<?php

namespace App\AssistantRAR\Tools;

class ReportTool extends BaseTool
{
    public function name(): string
    {
        return 'report.sales';
    }

    public function description(): string
    {
        return 'Generar reporte de ventas en un período específico.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
                'group_by' => ['type' => 'string', 'enum' => ['day', 'month', 'year'], 'description' => 'Agrupación'],
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
        return $this->success('Herramienta no implementada. Usa el módulo de reportes para esta acción.');
    }
}
