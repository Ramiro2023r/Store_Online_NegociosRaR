<?php

namespace App\Services;

use App\Models\Faq;

class FaqService
{
    public function search(?string $query = null, ?string $category = null): array
    {
        $q = Faq::ordered();
        if ($query) $q->where('question', 'ilike', "%{$query}%");
        if ($category) $q->where('category', $category);
        return $q->get()->toArray();
    }

    public function create(array $data): Faq
    {
        return Faq::create($data);
    }

    public function update(int $id, array $data): Faq
    {
        $faq = Faq::findOrFail($id);
        $faq->update($data);
        return $faq->fresh();
    }

    public function delete(int $id): void
    {
        Faq::findOrFail($id)->delete();
    }
}
