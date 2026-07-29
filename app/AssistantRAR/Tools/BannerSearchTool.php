<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerSearchTool extends BaseTool
{
    public function name(): string { return 'banner.search'; }
    public function description(): string { return 'Listar todos los banners ordenados.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Banners obtenidos.', ['banners' => app(BannerService::class)->search()]);
    }
}
