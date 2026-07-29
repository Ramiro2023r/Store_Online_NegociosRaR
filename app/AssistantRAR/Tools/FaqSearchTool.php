<?php

namespace App\AssistantRAR\Tools;

use App\Services\FaqService;

class FaqSearchTool extends BaseTool
{
    public function name(): string { return 'faq.search'; }
    public function description(): string { return 'Buscar FAQs por palabra clave o categoría.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'query' => ['type' => 'string', 'description' => 'Palabra clave'],
            'category' => ['type' => 'string', 'description' => 'Categoría'],
        ]];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('FAQs obtenidas.', ['faqs' => app(FaqService::class)->search($arguments['query'] ?? null, $arguments['category'] ?? null)]);
    }
}
