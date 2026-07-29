<?php

namespace App\AssistantRAR\Tools;

use App\Services\OrderService;

class OrderSearchTool extends BaseTool
{
    public function name(): string
    {
        return 'order.search';
    }

    public function description(): string
    {
        return 'Buscar pedidos por estado, cliente, rango de fechas o número de pedido.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => ['type' => 'string', 'enum' => ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'], 'description' => 'Estado del pedido'],
                'customer_id' => ['type' => 'integer', 'description' => 'ID del cliente'],
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
                'query' => ['type' => 'string', 'description' => 'Número de pedido'],
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
        $service = app(OrderService::class);
        $results = $service->search($arguments, $arguments['limit'] ?? 10);

        $orders = collect($results['data'] ?? [])->map(fn ($o) => [
            'id' => $o['id'],
            'order_number' => $o['order_number'],
            'status' => $o['status'],
            'total' => $o['total'],
            'customer' => $o['user']['name'] ?? null,
            'created_at' => $o['created_at'],
        ]);

        if ($orders->isEmpty()) {
            return $this->success('No se encontraron pedidos.', ['orders' => []]);
        }

        return $this->success("Se encontraron {$orders->count()} pedido(s).", [
            'orders' => $orders->values()->toArray(),
            'total' => $results['total'] ?? count($orders),
        ]);
    }
}
