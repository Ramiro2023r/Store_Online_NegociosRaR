<?php

namespace App\AssistantRAR\Tools;

use App\Services\BenefitService;

class BenefitDeleteTool extends BaseTool
{
    public function name(): string { return 'benefit.delete'; }
    public function description(): string { return 'Eliminar un beneficio.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(BenefitService::class)->delete($arguments['id']);
        return $this->success('Beneficio eliminado.', [], ['resource_type' => 'benefit', 'resource_id' => $arguments['id']]);
    }
}
