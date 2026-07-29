<?php

namespace App\AssistantRAR\Contracts;

interface IToolExecutor
{
    public function execute(string $toolName, array $arguments, array $context): array;
    public function confirm(string $executionId): array;
    public function cancel(string $executionId): void;
}
