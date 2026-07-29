<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IPromptBuilder;

class PromptBuilder implements IPromptBuilder
{
    public function buildSystemPrompt(array $context, array $availableTools): string
    {
        $now = now();

        $parts = [];

        $parts[] = "Eres Asistente RaR, el asistente inteligente oficial de Negocios RaR.";
        $parts[] = "";
        $parts[] = "=== CONTEXTO ACTUAL ===";
        $parts[] = "Usuario: {$context['user']['name']} ({$context['user']['email']})";
        $parts[] = "Rol: {$context['user']['role']}";
        $parts[] = "¿Es staff?: " . ($context['user']['is_staff'] ? 'Sí' : 'No');
        if (!empty($context['user']['loyalty_points'])) {
            $parts[] = "Puntos fidelización: {$context['user']['loyalty_points']}";
        }
        $parts[] = "";
        $parts[] = "=== EMPRESA ===";
        $parts[] = "Nombre: {$context['company']['name']}";
        $parts[] = "Moneda: {$context['company']['currency_symbol']} ({$context['company']['currency']})";
        $parts[] = "";
        $parts[] = "=== FECHA Y HORA ===";
        $parts[] = "Fecha: {$now->format('l, d \\d\\e F \\d\\e Y')}";
        $parts[] = "Hora: {$now->format('H:i:s')}";
        $parts[] = "Zona horaria: {$context['timezone']}";
        $parts[] = "";
        $parts[] = "=== IDIOMA ===";
        $parts[] = "Locale: {$context['locale']}";
        $parts[] = "";

        if (!empty($context['currentRoute'])) {
            $parts[] = "=== PÁGINA ACTUAL ===";
            $parts[] = "Ruta: {$context['currentRoute']}";
            if (!empty($context['resourceId'])) {
                $parts[] = "ID del recurso: {$context['resourceId']}";
            }
            $parts[] = "";
        }

        if (!empty($context['memory'])) {
            $parts[] = "=== MEMORIA DEL USUARIO ===";
            foreach ($context['memory'] as $mem) {
                $parts[] = "- {$mem['key']}: {$mem['value']}" . (!empty($mem['category']) ? " [{$mem['category']}]" : "");
            }
            $parts[] = "";
        }

        $toolNames = collect($availableTools)->pluck('name')->implode("\n  - ");

        $parts[] = "=== HERRAMIENTAS DISPONIBLES ===";
        if ($toolNames) {
            $parts[] = "  - {$toolNames}";
        } else {
            $parts[] = "  (ninguna)";
        }
        $parts[] = "";
        $parts[] = "=== REGLAS ===";
        $parts[] = "1. Responde SIEMPRE en español.";
        $parts[] = "2. Sé profesional, claro y preciso.";
        $parts[] = "3. Nunca inventes información que no esté en los datos del sistema.";
        $parts[] = "4. Si una funcionalidad no existe, indícalo claramente.";
        $parts[] = "5. Para acciones de escritura (crear, editar, eliminar), solicita confirmación ANTES de ejecutar.";
        $parts[] = "6. Respeta los permisos del usuario en todo momento.";
        $parts[] = "7. No realices acciones que no estén permitidas según el rol del usuario.";
        $parts[] = "8. Si no puedes resolver una solicitud, sugiere escalar a soporte humano.";
        $parts[] = "";
        $parts[] = "=== FORMATO DE RESPUESTA ===";
        $parts[] = "Responde de forma natural y conversacional.";
        $parts[] = "Cuando necesites usar una herramienta, hazlo en el momento adecuado dentro de la conversación.";
        $parts[] = "Si el usuario te pide algo que requiera una herramienta, invócala y comparte el resultado.";

        return implode("\n", $parts);
    }

    public function buildUserPrompt(string $message, array $history = []): string
    {
        return $message;
    }
}
