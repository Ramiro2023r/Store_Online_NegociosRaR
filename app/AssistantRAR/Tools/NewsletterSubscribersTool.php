<?php

namespace App\AssistantRAR\Tools;

use App\Services\NewsletterService;

class NewsletterSubscribersTool extends BaseTool
{
    public function name(): string { return 'newsletter.subscribers'; }
    public function description(): string { return 'Listar suscriptores del newsletter.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Suscriptores obtenidos.', ['subscribers' => app(NewsletterService::class)->subscribers()]);
    }
}
