<?php

namespace App\AssistantRAR\Tools;

use App\Services\FaqService;

class FaqUpdateTool extends BaseTool
{
    public function name(): string { return 'faq.update'; }
    public function description(): string { return 'Actualizar una FAQ.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer'],
            'question' => ['type' => 'string'],
            'answer' => ['type' => 'string'],
            'category' => ['type' => 'string'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id']; unset($arguments['id']);
        app(FaqService::class)->update($id, $arguments);
        return $this->success('FAQ actualizada.', [], ['resource_type' => 'faq', 'resource_id' => $id]);
    }
}
