<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'top_bar_text' => 'nullable|string|max:500',
            'footer_description' => 'nullable|string|max:500',
            'footer_address' => 'nullable|string|max:255',
            'footer_phone' => 'nullable|string|max:50',
            'footer_email' => 'nullable|string|max:100',
            'shipping_min_amount' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'about_mission' => 'nullable|string',
            'about_vision' => 'nullable|string',
            'about_values' => 'nullable|string',
            'about_clients_count' => 'nullable|string|max:50',
            'about_products_count' => 'nullable|string|max:50',
            'about_regions_count' => 'nullable|string|max:50',
            'about_rating' => 'nullable|string|max:50',
            'about_subtitle' => 'nullable|string',
            'home_title_categories' => 'nullable|string|max:200',
            'home_title_featured' => 'nullable|string|max:200',
            'home_title_newest' => 'nullable|string|max:200',
            'store_ruc' => 'nullable|string|max:20',
            'store_business_name' => 'nullable|string|max:255',
            'store_address' => 'nullable|string|max:255',
            'store_phone' => 'nullable|string|max:50',
            'store_email' => 'nullable|string|max:100',
            'store_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'store_logo_icon' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'abandoned_delay_hours' => 'nullable|integer|min:1|max:720',
            'abandoned_cart_subject' => 'nullable|string|max:255',
            'shipping_info' => 'nullable|string',
            'returns_policy' => 'nullable|string',
        ]);

        // Handle file uploads
        foreach (['store_logo', 'store_logo_icon'] as $key) {
            if ($request->hasFile($key)) {
                $data[$key] = $request->file($key)->store('store', 'public');
            } else {
                unset($data[$key]);
            }
        }

        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Configuración guardada.');
    }
}
