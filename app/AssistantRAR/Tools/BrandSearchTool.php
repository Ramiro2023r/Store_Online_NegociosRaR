<?php

namespace App\AssistantRAR\Tools;

use App\Services\BrandService;

class BrandSearchTool extends BaseTool
{
    public function name(): string
    {
        return 'brand.search';
    }

    public function description(): string
    {
        return 'Buscar marcas disponibles en los productos.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'query' => ['type' => 'string', 'description' => 'Término de búsqueda'],
            ],
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
        $service = app(BrandService::class);
        $results = $service->search($arguments['query'] ?? null);

        if (empty($results)) {
            return $this->success('No se encontraron marcas.', ['brands' => []]);
        }

        return $this->success("Se encontraron " . count($results) . " marca(s).", [
            'brands' => $results,
        ]);
    }
}
