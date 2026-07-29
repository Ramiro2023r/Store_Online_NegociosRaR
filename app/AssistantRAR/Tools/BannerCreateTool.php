<?php

namespace App\AssistantRAR\Tools;

use App\Services\BannerService;

class BannerCreateTool extends BaseTool
{
    public function name(): string { return 'banner.create'; }
    public function description(): string { return 'Crear un nuevo banner.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'title' => ['type' => 'string', 'description' => 'Título'],
            'subtitle' => ['type' => 'string'],
            'button_text' => ['type' => 'string'],
            'button_url' => ['type' => 'string'],
            'image' => ['type' => 'string', 'description' => 'URL de la imagen'],
        ], 'required' => ['title', 'image']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $banner = app(BannerService::class)->create($arguments);
        return $this->success('Banner creado.', ['banner' => $banner->toArray()], ['resource_type' => 'banner', 'resource_id' => $banner->id]);
    }
}
