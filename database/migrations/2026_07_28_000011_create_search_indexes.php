<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement('CREATE INDEX IF NOT EXISTS products_name_trgm_idx ON products USING GIN (name gin_trgm_ops)');
        DB::statement('CREATE INDEX IF NOT EXISTS products_brand_trgm_idx ON products USING GIN (COALESCE(brand, \'\') gin_trgm_ops)');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS products_name_trgm_idx');
        DB::statement('DROP INDEX IF EXISTS products_brand_trgm_idx');
    }
};
