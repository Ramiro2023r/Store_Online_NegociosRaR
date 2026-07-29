<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerDeactivateTool extends BaseTool
{
    public function name(): string { return 'banner.deactivate'; }
    public function description(): string { return 'Desactivar un banner.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del banner'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(BannerService::class)->deactivate($arguments['id']);
        return $this->success('Banner desactivado.', [], ['resource_type' => 'banner', 'resource_id' => $arguments['id']]);
    }
}
