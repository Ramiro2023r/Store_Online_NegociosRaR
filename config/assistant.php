<?php

return [
    'provider' => env('ASSISTANT_PROVIDER', 'groq'),
    'api_key' => env('ASSISTANT_API_KEY', ''),
    'model' => env('ASSISTANT_MODEL', 'llama-3.3-70b-versatile'),
    'max_tokens' => (int) env('ASSISTANT_MAX_TOKENS', 4096),
    'temperature' => (float) env('ASSISTANT_TEMPERATURE', 0.7),
    'timeout' => (int) env('ASSISTANT_TIMEOUT', 30),
    'streaming' => env('ASSISTANT_STREAMING', true),
    'context_limit' => (int) env('ASSISTANT_CONTEXT_LIMIT', 20),
    'confirmation_expiry' => (int) env('ASSISTANT_CONFIRMATION_EXPIRY', 600),
];
