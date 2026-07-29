<?php

namespace App\AssistantRAR\Tools;

use App\Services\CouponService;

class CouponValidateTool extends BaseTool
{
    public function name(): string { return 'coupon.validate'; }
    public function description(): string { return 'Validar un cupón de descuento por código.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'code' => ['type' => 'string', 'description' => 'Código del cupón'],
        ], 'required' => ['code']];
    }
    public function roles(): array { return ['admin', 'trabajador', 'cliente']; }
    public function confirmationLevel(): int { return 0; }
    public function execute(array $context, array $arguments): array
    {
        $result = app(CouponService::class)->validate($arguments['code'], $context['user']['id'], 0);
        return $result['valid']
            ? $this->success('Cupón válido.', $result)
            : $this->success($result['message'], ['valid' => false]);
    }
}
