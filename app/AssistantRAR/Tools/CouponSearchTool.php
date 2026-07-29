<?php

namespace App\AssistantRAR\Tools;

use App\Services\CouponService;

class CouponSearchTool extends BaseTool
{
    public function name(): string { return 'coupon.search'; }
    public function description(): string { return 'Buscar cupones por código.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'query' => ['type' => 'string', 'description' => 'Código o parte del código'],
        ]];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $coupons = app(CouponService::class)->search($arguments['query'] ?? null);
        if (empty($coupons)) return $this->success('No se encontraron cupones.', ['coupons' => []]);
        return $this->success('Cupones encontrados: ' . count($coupons), ['coupons' => $coupons]);
    }
}
