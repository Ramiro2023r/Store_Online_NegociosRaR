<?php

namespace App\AssistantRAR\Tools;

use App\Services\BenefitService;

class BenefitCreateTool extends BaseTool
{
    public function name(): string { return 'benefit.create'; }
    public function description(): string { return 'Crear un nuevo beneficio.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'icon' => ['type' => 'string', 'description' => 'Ícono emoji'],
            'title' => ['type' => 'string', 'description' => 'Título'],
        ], 'required' => ['icon', 'title']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $b = app(BenefitService::class)->create($arguments);
        return $this->success('Beneficio creado.', ['benefit' => $b->toArray()], ['resource_type' => 'benefit', 'resource_id' => $b->id]);
    }
}
