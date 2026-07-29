<?php

namespace App\Services;

use App\Models\Benefit;

class BenefitService
{
    public function search(): array
    {
        return Benefit::ordered()->get()->toArray();
    }

    public function create(array $data): Benefit
    {
        return Benefit::create($data);
    }

    public function update(int $id, array $data): Benefit
    {
        $benefit = Benefit::findOrFail($id);
        $benefit->update($data);
        return $benefit->fresh();
    }

    public function delete(int $id): void
    {
        Benefit::findOrFail($id)->delete();
    }
}
