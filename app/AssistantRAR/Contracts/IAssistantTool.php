<?php

namespace App\AssistantRAR\Contracts;

interface IAssistantTool
{
    public function name(): string;
    public function description(): string;
    public function inputSchema(): array;
    public function roles(): array;
    public function confirmationLevel(): int;
    public function execute(array $context, array $arguments): array;
}
