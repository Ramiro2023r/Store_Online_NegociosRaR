<?php

namespace App\AssistantRAR\Tools;

use App\Services\BenefitService;

class BenefitSearchTool extends BaseTool
{
    public function name(): string { return 'benefit.search'; }
    public function description(): string { return 'Listar todos los beneficios.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Beneficios obtenidos.', ['benefits' => app(BenefitService::class)->search()]);
    }
}
