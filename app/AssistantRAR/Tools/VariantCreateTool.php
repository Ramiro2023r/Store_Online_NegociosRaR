<?php

namespace App\AssistantRAR\Tools;

use App\Services\VariantService;

class VariantCreateTool extends BaseTool
{
    public function name(): string { return 'variant.create'; }
    public function description(): string { return 'Crear una nueva variante (talla/color) para un producto.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'product_id' => ['type' => 'integer'],
            'size' => ['type' => 'string', 'description' => 'Talla'],
            'color' => ['type' => 'string', 'description' => 'Color'],
            'sku' => ['type' => 'string'],
            'stock' => ['type' => 'integer', 'description' => 'Stock inicial'],
            'price' => ['type' => 'number', 'description' => 'Precio (opcional, hereda del producto)'],
        ], 'required' => ['product_id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $productId = $arguments['product_id']; unset($arguments['product_id']);
        $v = app(VariantService::class)->create($productId, $arguments);
        return $this->success('Variante creada.', ['variant' => $v->toArray()], ['resource_type' => 'variant', 'resource_id' => $v->id]);
    }
}
