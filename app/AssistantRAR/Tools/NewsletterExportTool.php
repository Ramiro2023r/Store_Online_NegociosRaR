<?php

namespace App\AssistantRAR\Tools;

use App\Services\NewsletterService;

class NewsletterExportTool extends BaseTool
{
    public function name(): string { return 'newsletter.export'; }
    public function description(): string { return 'Exportar lista de correos de suscriptores activos.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Exportación lista.', ['emails' => app(NewsletterService::class)->export()]);
    }
}
