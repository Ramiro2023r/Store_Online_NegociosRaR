<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerUpdateTool extends BaseTool
{
    public function name(): string { return 'banner.update'; }
    public function description(): string { return 'Actualizar datos de un banner.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
            'title' => ['type' => 'string'],
            'subtitle' => ['type' => 'string'],
            'button_text' => ['type' => 'string'],
            'button_url' => ['type' => 'string'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id']; unset($arguments['id']);
        app(BannerService::class)->update($id, $arguments);
        return $this->success('Banner actualizado.', [], ['resource_type' => 'banner', 'resource_id' => $id]);
    }
}
