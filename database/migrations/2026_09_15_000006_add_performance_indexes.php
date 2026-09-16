<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for performance optimization (IF NOT EXISTS for SQLite compatibility)
        DB::statement("CREATE INDEX IF NOT EXISTS 'products_category_id_index' ON 'products' ('category_id')");
        DB::statement("CREATE INDEX IF NOT EXISTS 'suppliers_category_id_index' ON 'suppliers' ('category_id')");
        DB::statement("CREATE INDEX IF NOT EXISTS 'clients_tier_index' ON 'clients' ('tier')");
        DB::statement("CREATE INDEX IF NOT EXISTS 'clients_status_index' ON 'clients' ('status')");
        DB::statement("CREATE INDEX IF NOT EXISTS 'clients_total_spent_index' ON 'clients' ('total_spent')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS 'products_category_id_index'");
        DB::statement("DROP INDEX IF EXISTS 'suppliers_category_id_index'");
        DB::statement("DROP INDEX IF EXISTS 'clients_tier_index'");
        DB::statement("DROP INDEX IF EXISTS 'clients_status_index'");
        DB::statement("DROP INDEX IF EXISTS 'clients_total_spent_index'");
    }
};
