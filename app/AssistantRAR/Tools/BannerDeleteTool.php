<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerDeleteTool extends BaseTool
{
    public function name(): string { return 'banner.delete'; }
    public function description(): string { return 'Eliminar un banner.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del banner'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(BannerService::class)->delete($arguments['id']);
        return $this->success('Banner eliminado.', [], ['resource_type' => 'banner', 'resource_id' => $arguments['id']]);
    }
}
