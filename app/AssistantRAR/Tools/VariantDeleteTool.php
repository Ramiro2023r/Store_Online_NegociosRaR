<?php

namespace App\AssistantRAR\Tools;

use App\Services\VariantService;

class VariantDeleteTool extends BaseTool
{
    public function name(): string { return 'variant.delete'; }
    public function description(): string { return 'Eliminar una variante.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(VariantService::class)->delete($arguments['id']);
        return $this->success('Variante eliminada.', [], ['resource_type' => 'variant', 'resource_id' => $arguments['id']]);
    }
}
