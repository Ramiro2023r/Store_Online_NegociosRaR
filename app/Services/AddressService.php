<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    public function list(int $userId): array
    {
        return Address::where('user_id', $userId)->latest()->get()->toArray();
    }

    public function create(int $userId, array $data): Address
    {
        $data['user_id'] = $userId;
        $address = Address::create($data);
        if ($address->is_default) {
            Address::where('user_id', $userId)->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        return $address->fresh();
    }

    public function update(int $userId, int $addressId, array $data): Address
    {
        $address = Address::where('user_id', $userId)->findOrFail($addressId);
        $address->update($data);
        if (!empty($data['is_default'])) {
            Address::where('user_id', $userId)->where('id', '!=', $address->id)->update(['is_default' => false]);
        }
        return $address->fresh();
    }

    public function setDefault(int $userId, int $addressId): Address
    {
        Address::where('user_id', $userId)->update(['is_default' => false]);
        $address = Address::where('user_id', $userId)->findOrFail($addressId);
        $address->update(['is_default' => true]);
        return $address->fresh();
    }

    public function delete(int $userId, int $addressId): void
    {
        Address::where('user_id', $userId)->findOrFail($addressId)->delete();
    }
}
