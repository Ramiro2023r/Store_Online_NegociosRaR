<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerGetTool extends BaseTool
{
    public function name(): string { return 'banner.get'; }
    public function description(): string { return 'Obtener detalle de un banner por su ID.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del banner'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $banner = app(BannerService::class)->find($arguments['id']);
        if (!$banner) return $this->error('Banner no encontrado.', 'NOT_FOUND');
        return $this->success('Banner obtenido.', ['banner' => $banner->toArray()]);
    }
}
