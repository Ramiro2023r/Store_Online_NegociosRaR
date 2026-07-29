<?php

namespace App\AssistantRAR\Tools;

use App\Services\OrderService;

class OrderTimelineTool extends BaseTool
{
    public function name(): string
    {
        return 'order.timeline';
    }

    public function description(): string
    {
        return 'Obtener la línea de tiempo de cambios de estado de un pedido.';
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
        $timeline = $service->timeline($arguments['id']);

        if (empty($timeline)) {
            return $this->success('El pedido no tiene cambios de estado registrados.', ['timeline' => []]);
        }

        return $this->success('Línea de tiempo obtenida.', [
            'timeline' => $timeline,
        ]);
    }
}
