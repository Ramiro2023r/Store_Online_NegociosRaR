<?php

namespace App\AssistantRAR\Tools;

use App\Services\SettingService;

class SettingGetAdminTool extends BaseTool
{
    public function name(): string { return 'setting.get_admin'; }
    public function description(): string { return 'Obtener toda la configuración del sistema (admin).'; }
    public function inputSchema(): array { return ['type' => 'object', 'properties' => []]; }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        return $this->success('Configuración obtenida.', ['settings' => app(SettingService::class)->getAdmin()]);
    }
}
