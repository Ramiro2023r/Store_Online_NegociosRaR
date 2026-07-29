<?php

namespace App\AssistantRAR\Services;

use App\AssistantRAR\Contracts\IProviderManager;

class ProviderManager implements IProviderManager
{
    private string $provider;
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->provider = config('assistant.provider', 'groq');
        $this->apiKey = config('assistant.api_key', '');
        $this->model = config('assistant.model', 'llama-3.3-70b-versatile');
    }

    public function sendMessage(array $messages, array $tools, array $context): array
    {
        if (empty($this->apiKey)) {
            return [
                'content' => 'El asistente no está configurado. Contacta al administrador.',
                'tool_calls' => [],
            ];
        }

        return match ($this->provider) {
            'groq' => $this->callGroq($messages, $tools),
            default => $this->callOpenAI($messages, $tools),
        };
    }

    public function sendMessageStream(array $messages, array $tools, array $context, callable $onChunk): void
    {
        if (empty($this->apiKey)) {
            $onChunk('El asistente no está configurado. Contacta al administrador.');
            return;
        }

        match ($this->provider) {
            'groq' => $this->callGroqStream($messages, $tools, $onChunk),
            default => $this->callOpenAIStream($messages, $tools, $onChunk),
        };
    }

    public function getProviderName(): string
    {
        return $this->provider;
    }

    private function callGroq(array $messages, array $tools): array
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4096,
        ];

        if (!empty($tools)) {
            $body['tools'] = $this->formatToolsForGroq($tools);
            $body['tool_choice'] = 'auto';
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($url, $body);

        if ($response->failed()) {
            return [
                'content' => 'Error al conectar con el asistente. Intenta nuevamente.',
                'tool_calls' => [],
            ];
        }

        $data = $response->json();
        $choice = $data['choices'][0]['message'] ?? [];

        return [
            'content' => $choice['content'] ?? '',
            'tool_calls' => $this->parseToolCalls($choice['tool_calls'] ?? []),
        ];
    }

    private function callGroqStream(array $messages, array $tools, callable $onChunk): void
    {
        $url = 'https://api.groq.com/openai/v1/chat/completions';

        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4096,
            'stream' => true,
        ];

        if (!empty($tools)) {
            $body['tools'] = $this->formatToolsForGroq($tools);
            $body['tool_choice'] = 'auto';
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(60)->withOptions(['stream' => true])->post($url, $body);

        $buffer = '';
        foreach (explode("\n", $response->body()) as $line) {
            if (str_starts_with($line, 'data: ')) {
                $json = substr($line, 6);
                if ($json === '[DONE]') break;
                $chunk = json_decode($json, true);
                $delta = $chunk['choices'][0]['delta']['content'] ?? '';
                if ($delta !== '') {
                    $onChunk($delta);
                }
            }
        }
    }

    private function callOpenAI(array $messages, array $tools): array
    {
        $url = 'https://api.openai.com/v1/chat/completions';

        $body = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4096,
        ];

        if (!empty($tools)) {
            $body['tools'] = $this->formatToolsForGroq($tools);
            $body['tool_choice'] = 'auto';
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($url, $body);

        if ($response->failed()) {
            return [
                'content' => 'Error al conectar con el asistente. Intenta nuevamente.',
                'tool_calls' => [],
            ];
        }

        $data = $response->json();
        $choice = $data['choices'][0]['message'] ?? [];

        return [
            'content' => $choice['content'] ?? '',
            'tool_calls' => $this->parseToolCalls($choice['tool_calls'] ?? []),
        ];
    }

    private function callOpenAIStream(array $messages, array $tools, callable $onChunk): void
    {
        $this->callGroqStream($messages, $tools, $onChunk);
    }

    private function formatToolsForGroq(array $tools): array
    {
        return array_map(function ($tool) {
            $params = $tool['input_schema'] ?? ['type' => 'object', 'properties' => []];
            if (isset($params['properties']) && is_array($params['properties']) && $params['properties'] === []) {
                $params['properties'] = new \stdClass();
            }
            return [
                'type' => 'function',
                'function' => [
                    'name' => $tool['name'],
                    'description' => $tool['description'] ?? '',
                    'parameters' => $params,
                ],
            ];
        }, $tools);
    }

    private function parseToolCalls(array $toolCalls): array
    {
        return array_map(function ($call) {
            return [
                'id' => $call['id'] ?? '',
                'name' => $call['function']['name'] ?? '',
                'arguments' => json_decode($call['function']['arguments'] ?? '{}', true) ?? [],
            ];
        }, $toolCalls);
    }
}
