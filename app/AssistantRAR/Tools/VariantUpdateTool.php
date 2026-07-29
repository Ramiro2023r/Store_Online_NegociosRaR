<?php

namespace App\AssistantRAR\Tools;

use App\Services\VariantService;

class VariantUpdateTool extends BaseTool
{
    public function name(): string { return 'variant.update'; }
    public function description(): string { return 'Actualizar datos de una variante.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
            'size' => ['type' => 'string'],
            'color' => ['type' => 'string'],
            'sku' => ['type' => 'string'],
            'price' => ['type' => 'number'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id']; unset($arguments['id']);
        app(VariantService::class)->update($id, $arguments);
        return $this->success('Variante actualizada.', [], ['resource_type' => 'variant', 'resource_id' => $id]);
    }
}
