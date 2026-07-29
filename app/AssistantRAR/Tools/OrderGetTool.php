<?php

namespace App\AssistantRAR\Tools;

use App\Services\OrderService;

class OrderGetTool extends BaseTool
{
    public function name(): string
    {
        return 'order.get';
    }

    public function description(): string
    {
        return 'Obtener el detalle completo de un pedido por su ID.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'id' => ['type' => 'integer', 'description' => 'ID del pedido'],
            ],
            'required' => ['id'],
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
        $order = $service->find($arguments['id']);

        if (!$order) {
            return $this->error('Pedido no encontrado.', 'NOT_FOUND');
        }

        return $this->success('Pedido encontrado.', [
            'order' => $order->toArray(),
        ]);
    }
}
