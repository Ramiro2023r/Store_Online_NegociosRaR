<?php

namespace App\AssistantRAR\Tools;

class OrderTool extends BaseTool
{
    public function name(): string
    {
        return 'order.search';
    }

    public function description(): string
    {
        return 'Buscar pedidos por rango de fechas, estado, cliente o monto.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'description' => 'Estado del pedido'],
                'customer_id' => ['type' => 'integer', 'description' => 'ID del cliente'],
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
                'limit' => ['type' => 'integer', 'description' => 'Máximo de resultados'],
            ],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador', 'cliente'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        return $this->success('Herramienta no implementada. Usa el panel de pedidos para esta acción.');
    }
}
