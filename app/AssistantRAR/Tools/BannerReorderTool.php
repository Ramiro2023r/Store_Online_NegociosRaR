<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerReorderTool extends BaseTool
{
    public function name(): string { return 'banner.reorder'; }
    public function description(): string { return 'Reordenar banners (enviar array de IDs en el orden deseado).'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'ids' => ['type' => 'array', 'items' => ['type' => 'integer'], 'description' => 'IDs en el nuevo orden'],
        ], 'required' => ['ids']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(BannerService::class)->reorder($arguments['ids']);
        return $this->success('Banners reordenados.');
    }
}
