<?php

namespace App\AssistantRAR\DTO;

final class ToolResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $data = [],
        public readonly ?string $errorCode = null,
        public readonly array $metadata = [],
    ) {}

    public static function success(string $message, array $data = [], array $metadata = []): self
    {
        return new self(true, $message, $data, null, $metadata);
    }

    public static function error(string $message, string $errorCode = 'INTERNAL_ERROR', array $metadata = []): self
    {
        return new self(false, $message, [], $errorCode, $metadata);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'errorCode' => $this->errorCode,
            'metadata' => $this->metadata,
        ];
    }
}
