<?php

namespace Tests\Unit;

use App\AssistantRAR\Services\PromptBuilder;
use Tests\TestCase;

class PromptBuilderTest extends TestCase
{
    private PromptBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = new PromptBuilder();
    }

    public function test_build_system_prompt_contains_user_info(): void
    {
        $context = [
            'user' => ['name' => 'Juan Pérez', 'email' => 'juan@test.com', 'role' => 'admin', 'is_staff' => true, 'loyalty_points' => 150],
            'company' => ['name' => 'Negocios RaR', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'America/Lima',
        ];

        $prompt = $this->builder->buildSystemPrompt($context, []);

        $this->assertStringContainsString('Juan Pérez', $prompt);
        $this->assertStringContainsString('juan@test.com', $prompt);
        $this->assertStringContainsString('admin', $prompt);
        $this->assertStringContainsString('Negocios RaR', $prompt);
        $this->assertStringContainsString('PEN', $prompt);
        $this->assertStringContainsString('150', $prompt);
    }

    public function test_build_system_prompt_lists_tools(): void
    {
        $context = [
            'user' => ['name' => 'Admin', 'email' => 'a@b.com', 'role' => 'admin', 'is_staff' => true],
            'company' => ['name' => 'Tienda', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'UTC',
        ];

        $tools = [
            ['name' => 'product.search', 'description' => 'Buscar productos'],
            ['name' => 'product.create', 'description' => 'Crear producto'],
        ];

        $prompt = $this->builder->buildSystemPrompt($context, $tools);

        $this->assertStringContainsString('product.search', $prompt);
        $this->assertStringContainsString('product.create', $prompt);
    }

    public function test_build_system_prompt_shows_none_when_no_tools(): void
    {
        $context = [
            'user' => ['name' => 'Client', 'email' => 'c@d.com', 'role' => 'cliente', 'is_staff' => false],
            'company' => ['name' => 'Tienda', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'UTC',
        ];

        $prompt = $this->builder->buildSystemPrompt($context, []);

        $this->assertStringContainsString('(ninguna)', $prompt);
    }

    public function test_build_system_prompt_includes_rules(): void
    {
        $context = [
            'user' => ['name' => 'U', 'email' => 'u@u.com', 'role' => 'cliente', 'is_staff' => false],
            'company' => ['name' => 'T', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'UTC',
        ];

        $prompt = $this->builder->buildSystemPrompt($context, []);

        $this->assertStringContainsString('español', $prompt);
        $this->assertStringContainsString('confirma', $prompt);
    }

    public function test_build_system_prompt_includes_memory(): void
    {
        $context = [
            'user' => ['name' => 'U', 'email' => 'u@u.com', 'role' => 'cliente', 'is_staff' => false],
            'company' => ['name' => 'T', 'currency_symbol' => 'S/', 'currency' => 'PEN'],
            'locale' => 'es',
            'timezone' => 'UTC',
            'memory' => [
                ['key' => 'talla_preferida', 'value' => '42', 'category' => 'preferencias'],
                ['key' => 'color_favorito', 'value' => 'azul', 'category' => null],
            ],
        ];

        $prompt = $this->builder->buildSystemPrompt($context, []);

        $this->assertStringContainsString('talla_preferida', $prompt);
        $this->assertStringContainsString('color_favorito', $prompt);
        $this->assertStringContainsString('preferencias', $prompt);
    }

    public function test_build_user_prompt_returns_message(): void
    {
        $result = $this->builder->buildUserPrompt('Hola mundo', []);
        $this->assertEquals('Hola mundo', $result);
    }
}
