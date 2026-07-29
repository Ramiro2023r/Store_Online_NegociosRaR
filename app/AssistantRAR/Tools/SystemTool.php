<?php

namespace App\AssistantRAR\Tools;

class SystemTool extends BaseTool
{
    public function name(): string
    {
        return 'system.capabilities';
    }

    public function description(): string
    {
        return 'Listar todas las capacidades y herramientas disponibles del asistente.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [],
        ];
    }

    public function roles(): array
    {
        return ['admin', 'trabajador', 'cliente'];
    }

    public function confirmationLevel(): int
    {
        return 0;
    }

    public function execute(array $context, array $arguments): array
    {
        return $this->success('Puedo ayudarte a buscar productos, gestionar tu carrito, consultar pedidos, y más. Usa el panel correspondiente para acciones avanzadas.');
    }
}
