<?php

namespace App\AssistantRAR\Contracts;

interface IProviderManager
{
    public function sendMessage(array $messages, array $tools, array $context): array;
    public function sendMessageStream(array $messages, array $tools, array $context, callable $onChunk): void;
    public function getProviderName(): string;
}
