<?php

namespace App\AssistantRAR\Tools;

use App\Services\VariantService;

class VariantSearchTool extends BaseTool
{
    public function name(): string { return 'variant.search'; }
    public function description(): string { return 'Listar variantes de un producto (talla/color).'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'product_id' => ['type' => 'integer'],
        ], 'required' => ['product_id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Variantes obtenidas.', ['variants' => app(VariantService::class)->search($arguments['product_id'])]);
    }
}
