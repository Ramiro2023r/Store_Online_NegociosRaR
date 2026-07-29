<?php

namespace App\AssistantRAR\Tools;

use App\Services\CouponService;

class CouponUpdateTool extends BaseTool
{
    public function name(): string { return 'coupon.update'; }
    public function description(): string { return 'Actualizar datos de un cupón.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'id' => ['type' => 'integer', 'description' => 'ID del cupón'],
            'value' => ['type' => 'number', 'description' => 'Nuevo valor'],
            'min_purchase' => ['type' => 'number'],
            'max_discount' => ['type' => 'number'],
            'usage_limit' => ['type' => 'integer'],
            'expires_at' => ['type' => 'string'],
        ], 'required' => ['id']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $id = $arguments['id']; unset($arguments['id']);
        $coupon = app(CouponService::class)->update($id, $arguments);
        return $this->success('Cupón actualizado.', ['coupon' => $coupon->toArray()], ['resource_type' => 'coupon', 'resource_id' => $id]);
    }
}
