<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->timestamp('last_active_at')->nullable()->after('session_id');
        });

        DB::table('carts')->update(['last_active_at' => now()]);

        DB::table('settings')->updateOrInsert(
            ['key' => 'abandoned_delay_hours'],
            ['value' => '24', 'created_at' => now(), 'updated_at' => now()]
        );

        DB::table('settings')->updateOrInsert(
            ['key' => 'abandoned_cart_subject'],
            ['value' => '¡No olvides tu carrito! Tienes productos esperándote', 'created_at' => now(), 'updated_at' => now()]
        );
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('last_active_at');
        });

        DB::table('settings')->whereIn('key', ['abandoned_delay_hours', 'abandoned_cart_subject'])->delete();
    }
};
