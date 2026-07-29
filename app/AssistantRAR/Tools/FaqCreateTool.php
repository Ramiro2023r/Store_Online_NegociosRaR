<?php

namespace App\AssistantRAR\Tools;

use App\Services\FaqService;

class FaqCreateTool extends BaseTool
{
    public function name(): string { return 'faq.create'; }
    public function description(): string { return 'Crear una nueva FAQ.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'question' => ['type' => 'string', 'description' => 'Pregunta'],
            'answer' => ['type' => 'string', 'description' => 'Respuesta'],
            'category' => ['type' => 'string', 'description' => 'Categoría'],
        ], 'required' => ['question', 'answer']];
    }
    public function roles(): array { return ['admin', 'trabajador']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $faq = app(FaqService::class)->create($arguments);
        return $this->success('FAQ creada.', ['faq' => $faq->toArray()], ['resource_type' => 'faq', 'resource_id' => $faq->id]);
    }
}
