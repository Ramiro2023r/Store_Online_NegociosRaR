<?php

namespace App\AssistantRAR\Tools;

use App\Services\InventoryService;

class InventoryMovementsTool extends BaseTool
{
    public function name(): string
    {
        return 'inventory.movements';
    }

    public function description(): string
    {
        return 'Consultar historial de movimientos de stock (ventas, reabastecimientos, ajustes).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'product_id' => ['type' => 'integer', 'description' => 'Filtrar por producto'],
                'type' => ['type' => 'string', 'enum' => ['sale', 'restock', 'adjustment'], 'description' => 'Tipo de movimiento'],
                'date_from' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha inicio'],
                'date_to' => ['type' => 'string', 'format' => 'date', 'description' => 'Fecha fin'],
                'limit' => ['type' => 'integer', 'description' => 'Máximo de resultados'],
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
        $service = app(InventoryService::class);
        $results = $service->movements($arguments, $arguments['limit'] ?? 30);

        if (empty($results)) {
            return $this->success('No se encontraron movimientos.', ['movements' => []]);
        }

        return $this->success("Se encontraron " . count($results) . " movimiento(s).", [
            'movements' => $results,
        ]);
    }
}
