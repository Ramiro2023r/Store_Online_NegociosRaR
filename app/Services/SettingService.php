<?php

namespace App\Services;

use App\Models\Setting;

class SettingService
{
    public function getPublic(): array
    {
        return [
            'store_name' => Setting::getValue('store_name', 'Negocios RaR'),
            'store_slogan' => Setting::getValue('store_slogan', 'Tu tienda online de confianza'),
            'store_email' => Setting::getValue('store_email', 'ventas@negociosrar.com'),
            'store_phone' => Setting::getValue('store_phone', '(01) 555-0100'),
            'store_address' => Setting::getValue('store_address', 'Lima, Perú'),
            'currency' => config('app.currency', 'PEN'),
        ];
    }

    public function getAdmin(): array
    {
        return [
            'store_name' => Setting::getValue('store_name', 'Negocios RaR'),
            'store_slogan' => Setting::getValue('store_slogan', ''),
            'store_description' => Setting::getValue('store_description', ''),
            'store_email' => Setting::getValue('store_email', ''),
            'store_phone' => Setting::getValue('store_phone', ''),
            'store_address' => Setting::getValue('store_address', ''),
            'free_shipping_min' => Setting::getValue('free_shipping_min', 0),
            'shipping_cost' => Setting::getValue('shipping_cost', 0),
            'points_earning_rate' => Setting::getValue('points_earning_rate', 1),
            'points_redeem_rate' => Setting::getValue('points_redeem_rate', 1),
            'footer_description' => Setting::getValue('footer_description', ''),
            'footer_phone' => Setting::getValue('footer_phone', ''),
            'footer_email' => Setting::getValue('footer_email', ''),
            'footer_address' => Setting::getValue('footer_address', ''),
            'store_logo_icon' => Setting::getValue('store_logo_icon', ''),
            'store_logo_light' => Setting::getValue('store_logo_light', ''),
            'meta_description' => Setting::getValue('meta_description', ''),
            'meta_keywords' => Setting::getValue('meta_keywords', ''),
        ];
    }

    public function update(array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::setValue($key, $value);
        }
    }
}
