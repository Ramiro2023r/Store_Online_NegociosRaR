<?php

namespace App\AssistantRAR\Tools;

use App\Services\SettingService;

class SettingGetPublicTool extends BaseTool
{
    public function name(): string { return 'setting.get_public'; }
    public function description(): string { return 'Obtener configuración pública de la tienda.'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Configuración obtenida.', ['settings' => app(SettingService::class)->getPublic()]);
    }
}
