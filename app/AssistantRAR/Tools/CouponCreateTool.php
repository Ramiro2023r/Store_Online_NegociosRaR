<?php

namespace App\AssistantRAR\Tools;

use App\Services\CouponService;

class CouponCreateTool extends BaseTool
{
    public function name(): string { return 'coupon.create'; }
    public function description(): string { return 'Crear un nuevo cupón de descuento.'; }
    public function inputSchema(): array {
        return ['type' => 'object', 'properties' => [
            'code' => ['type' => 'string', 'description' => 'Código del cupón'],
            'type' => ['type' => 'string', 'enum' => ['percentage', 'fixed'], 'description' => 'Tipo: percentage o fixed'],
            'value' => ['type' => 'number', 'description' => 'Valor del descuento'],
            'min_purchase' => ['type' => 'number', 'description' => 'Compra mínima'],
            'max_discount' => ['type' => 'number', 'description' => 'Descuento máximo'],
            'usage_limit' => ['type' => 'integer', 'description' => 'Límite de usos total'],
            'usage_limit_per_user' => ['type' => 'integer', 'description' => 'Límite por usuario'],
            'category_id' => ['type' => 'integer', 'description' => 'Categoría restringida'],
            'starts_at' => ['type' => 'string', 'description' => 'Fecha inicio (Y-m-d)'],
            'expires_at' => ['type' => 'string', 'description' => 'Fecha expiración (Y-m-d)'],
        ], 'required' => ['code', 'type', 'value']];
    }
    public function roles(): array { return ['admin']; }
    public function confirmationLevel(): int { return 1; }
    public function execute(array $context, array $arguments): array
    {
        $coupon = app(CouponService::class)->create($arguments);
        return $this->success("Cupón '{$coupon->code}' creado.", ['coupon' => $coupon->toArray()], ['resource_type' => 'coupon', 'resource_id' => $coupon->id]);
    }
}
