<?php

namespace App\Services;

use App\Models\Banner;

class BannerService
{
    public function search(): array
    {
        return Banner::ordered()->get()->toArray();
    }

    public function find(int $id): ?Banner
    {
        return Banner::find($id);
    }

    public function create(array $data): Banner
    {
        return Banner::create($data);
    }

    public function update(int $id, array $data): Banner
    {
        $banner = Banner::findOrFail($id);
        $banner->update($data);
        return $banner->fresh();
    }

    public function activate(int $id): Banner
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['active' => true]);
        return $banner->fresh();
    }

    public function deactivate(int $id): Banner
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['active' => false]);
        return $banner->fresh();
    }

    public function delete(int $id): void
    {
        Banner::findOrFail($id)->delete();
    }

    public function reorder(array $ids): void
    {
        foreach ($ids as $i => $id) {
            Banner::where('id', $id)->update(['sort_order' => $i]);
        }
    }
}
