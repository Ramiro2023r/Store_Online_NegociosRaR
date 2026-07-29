<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IToolExecutor;
use App\AssistantRAR\Contracts\IToolRegistry;
use App\AssistantRAR\DTO\ToolResult;
use App\AssistantRAR\Models\AssistantToolLog;

class ToolExecutor implements IToolExecutor
{
    public function __construct(
        private readonly IToolRegistry $registry,
    ) {}

    public function execute(string $toolName, array $arguments, array $context): array
    {
        $tool = $this->registry->get($toolName);

        if (!$tool) {
            return ToolResult::error("Herramienta '{$toolName}' no encontrada.", 'TOOL_NOT_FOUND')->toArray();
        }

        if (!$tool['enabled']) {
            return ToolResult::error("Herramienta '{$toolName}' no disponible.", 'TOOL_DISABLED')->toArray();
        }

        $role = $context['user']['role'] ?? 'cliente';
        if (!in_array($role, $tool['roles'])) {
            return ToolResult::error('No tienes permiso para usar esta herramienta.', 'FORBIDDEN')->toArray();
        }

        try {
            /** @var \App\AssistantRAR\Contracts\IAssistantTool $handler */
            $handler = app($tool['handler']);
            $result = $handler->execute($context, $arguments);

            AssistantToolLog::create([
                'user_id' => $context['user']['id'],
                'conversation_id' => $context['conversation_id'] ?? null,
                'message_id' => $context['message_id'] ?? null,
                'tool_name' => $toolName,
                'arguments' => $arguments,
                'status' => $result['success'] ? 'completed' : 'failed',
                'result_message' => $result['message'],
                'error_code' => $result['errorCode'],
                'resource_type' => $result['metadata']['resource_type'] ?? null,
                'resource_id' => $result['metadata']['resource_id'] ?? null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return $result;
        } catch (\Throwable $e) {
            AssistantToolLog::create([
                'user_id' => $context['user']['id'],
                'conversation_id' => $context['conversation_id'] ?? null,
                'tool_name' => $toolName,
                'arguments' => $arguments,
                'status' => 'failed',
                'result_message' => $e->getMessage(),
                'error_code' => 'INTERNAL_ERROR',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            return ToolResult::error('Error interno al ejecutar la herramienta.', 'INTERNAL_ERROR')->toArray();
        }
    }

    public function confirm(string $executionId): array
    {
        return ToolResult::success('Acción confirmada.')->toArray();
    }

    public function cancel(string $executionId): void
    {
        AssistantToolLog::where('id', $executionId)->update(['status' => 'cancelled']);
    }
}
