<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            ['key' => 'store_logo', 'value' => 'images/Mejoradelogo.svg'],
            ['key' => 'store_logo_icon', 'value' => 'images/Mejoradelogoiconoapp.svg'],
            ['key' => 'store_ruc', 'value' => '20600000000'],
            ['key' => 'store_business_name', 'value' => 'Negocios RaR E.I.R.L.'],
            ['key' => 'store_address', 'value' => 'Av. Principal 123, Lima, Perú'],
            ['key' => 'store_phone', 'value' => '(01) 555-0100'],
            ['key' => 'store_email', 'value' => 'ventas@negociosrar.com'],
            ['key' => 'store_about_subtitle', 'value' => 'Somos una tienda online peruana dedicada a ofrecer productos de calidad en tecnología, moda, hogar, deportes, belleza y mucho más, con la confianza y rapidez que mereces.'],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'store_logo', 'store_logo_icon', 'store_ruc', 'store_business_name',
            'store_address', 'store_phone', 'store_email', 'store_about_subtitle',
        ])->delete();
    }
};
