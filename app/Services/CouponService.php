<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function search(?string $query = null): array
    {
        $q = Coupon::query();
        if ($query) {
            $q->where('code', 'ilike', "%{$query}%");
        }
        return $q->latest()->get()->toArray();
    }

    public function find(int $id): ?Coupon
    {
        return Coupon::find($id);
    }

    public function create(array $data): Coupon
    {
        $data['code'] = strtoupper($data['code'] ?? '');
        return Coupon::create($data);
    }

    public function update(int $id, array $data): Coupon
    {
        $coupon = Coupon::findOrFail($id);
        if (isset($data['code'])) $data['code'] = strtoupper($data['code']);
        $coupon->update($data);
        return $coupon->fresh();
    }

    public function activate(int $id): Coupon
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['active' => true]);
        return $coupon->fresh();
    }

    public function deactivate(int $id): Coupon
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->update(['active' => false]);
        return $coupon->fresh();
    }

    public function delete(int $id): void
    {
        Coupon::findOrFail($id)->delete();
    }

    public function validate(string $code, int $userId, float $subtotal): array
    {
        $coupon = Coupon::findByCode($code);
        if (!$coupon) return ['valid' => false, 'message' => 'Cupón no encontrado.'];
        if (!$coupon->isValid()) return ['valid' => false, 'message' => 'Cupón expirado o inactivo.'];
        if (!$coupon->isValidForUser(\App\Models\User::find($userId))) {
            return ['valid' => false, 'message' => 'Límite de usos alcanzado para este usuario.'];
        }
        return ['valid' => true, 'coupon' => $coupon->toArray()];
    }
}
