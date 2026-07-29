<?php

namespace App\AssistantRAR\Tools;

use App\Services\FaqService;

class FaqDeleteTool extends BaseTool
{
    public function name(): string { return 'faq.delete'; }
    public function description(): string { return 'Eliminar una FAQ.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(FaqService::class)->delete($arguments['id']);
        return $this->success('FAQ eliminada.', [], ['resource_type' => 'faq', 'resource_id' => $arguments['id']]);
    }
}
