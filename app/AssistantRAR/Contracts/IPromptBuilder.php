<?php

namespace App\AssistantRAR\Contracts;

interface IPromptBuilder
{
    public function buildSystemPrompt(array $context, array $availableTools): string;
    public function buildUserPrompt(string $message, array $history = []): string;
}
