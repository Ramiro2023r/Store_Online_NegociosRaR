<?php

namespace App\AssistantRAR\Tools;

use App\Services\VariantService;

class VariantUpdateStockTool extends BaseTool
{
    public function name(): string { return 'variant.update_stock'; }
    public function description(): string { return 'Actualizar el stock de una variante.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
            'stock' => ['type' => 'integer', 'description' => 'Nuevo stock'],
        ], 'required' => ['id', 'stock']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $v = app(VariantService::class)->updateStock($arguments['id'], $arguments['stock']);
        return $this->success('Stock de variante actualizado.', ['variant' => $v->toArray()], ['resource_type' => 'variant', 'resource_id' => $arguments['id']]);
    }
}
