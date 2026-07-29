<?php

namespace App\AssistantRAR\Tools;

use App\Services\BenefitService;

class BenefitUpdateTool extends BaseTool
{
    public function name(): string { return 'benefit.update'; }
    public function description(): string { return 'Actualizar un beneficio.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
            'icon' => ['type' => 'string'],
            'title' => ['type' => 'string'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id']; unset($arguments['id']);
        app(BenefitService::class)->update($id, $arguments);
        return $this->success('Beneficio actualizado.', [], ['resource_type' => 'benefit', 'resource_id' => $id]);
    }
}
