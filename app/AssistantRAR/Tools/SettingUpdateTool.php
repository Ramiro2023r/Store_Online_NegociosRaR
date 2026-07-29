<?php

namespace App\AssistantRAR\Tools;

use App\Services\SettingService;

class SettingUpdateTool extends BaseTool
{
    public function name(): string { return 'setting.update'; }
    public function description(): string { return 'Actualizar configuración del sistema (clave → valor). Se pueden enviar múltiples pares.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [], 'description' => 'Objeto clave → valor. Ej: {"store_name": "Mi Tienda", "store_email": "x@y.com"}'];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(SettingService::class)->update($arguments);
        return $this->success('Configuración actualizada.');
    }
}
