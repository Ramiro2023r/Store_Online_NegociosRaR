<?php

namespace App\Services;

use App\Models\Newsletter;

class NewsletterService
{
    public function subscribers(): array
    {
        return Newsletter::latest()->get()->toArray();
    }

    public function delete(int $id): void
    {
        Newsletter::findOrFail($id)->delete();
    }

    public function export(): array
    {
        return Newsletter::where('active', true)->pluck('email')->toArray();
    }
}
