<?php

namespace App\AssistantRAR\Tools;

class ReportExportCsvTool extends BaseTool
{
    public function name(): string
    {
        return 'report.export_csv';
    }

    public function description(): string
    {
        return 'Exportar reporte de ventas. Usa el panel de administración para descargar el archivo CSV.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'type' => ['type' => 'string', 'enum' => ['period', 'category', 'product'], 'description' => 'Tipo de reporte'],
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
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
        return $this->success('Para exportar CSV, ve al módulo de Reportes en el panel de administración y usa el botón de exportar.');
    }
}
