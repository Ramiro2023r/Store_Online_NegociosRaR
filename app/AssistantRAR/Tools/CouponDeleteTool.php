<?php

namespace App\AssistantRAR\Tools;

use App\Services\CouponService;

class CouponDeleteTool extends BaseTool
{
    public function name(): string { return 'coupon.delete'; }
    public function description(): string { return 'Eliminar un cupón permanentemente.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del cupón'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 2; }
    public function execute(array $context, array $arguments): array
    {
        app(CouponService::class)->delete($arguments['id']);
        return $this->success('Cupón eliminado.', [], ['resource_type' => 'coupon', 'resource_id' => $arguments['id']]);
    }
}
