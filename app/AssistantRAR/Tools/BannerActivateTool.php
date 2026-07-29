<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerActivateTool extends BaseTool
{
    public function name(): string { return 'banner.activate'; }
    public function description(): string { return 'Activar un banner.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del banner'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(BannerService::class)->activate($arguments['id']);
        return $this->success('Banner activado.', [], ['resource_type' => 'banner', 'resource_id' => $arguments['id']]);
    }
}
