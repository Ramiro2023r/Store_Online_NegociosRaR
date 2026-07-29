<?php

namespace App\AssistantRAR\Tools;

use App\Services\OrderService;

class OrderUpdateStatusTool extends BaseTool
{
    public function name(): string
    {
        return 'order.update_status';
    }

    public function description(): string
    {
        return 'Actualizar el estado de un pedido. Envía notificación al cliente.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del pedido'],
                'status' => ['type' => 'string', 'enum' => ['pendiente', 'pagado', 'enviado', 'entregado', 'cancelado'], 'description' => 'Nuevo estado'],
            ],
            'required' => ['id', 'status'],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador'];
    }

    public function confirmationLevel(): int
    {
        return 1;
    }

    public function execute(array $context, array $arguments): array
    {
        $service = app(OrderService::class);
        $order = $service->updateStatus($arguments['id'], $arguments['status']);

        return $this->success("Pedido #{$order->order_number} actualizado a '{$order->status}'.", [
            'order' => ['id' => $order->id, 'order_number' => $order->order_number, 'status' => $order->status],
        ], ['resource_type' => 'order', 'resource_id' => $arguments['id']]);
    }
}
