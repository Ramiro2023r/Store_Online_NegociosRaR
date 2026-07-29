<?php

namespace App\AssistantRAR\Tools;

use App\Services\CouponService;

class CouponDeactivateTool extends BaseTool
{
    public function name(): string { return 'coupon.deactivate'; }
    public function description(): string { return 'Desactivar un cupón.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del cupón'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        app(CouponService::class)->deactivate($arguments['id']);
        return $this->success('Cupón desactivado.', [], ['resource_type' => 'coupon', 'resource_id' => $arguments['id']]);
    }
}
