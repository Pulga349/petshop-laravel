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
        Schema::table('suppliers', function (Blueprint $table) {
            // Add polymorphic columns
            $table->unsignedBigInteger('category_id')->nullable()->after('contact_person');
            $table->string('category_type')->nullable()->after('category_id');
            $table->index(['category_id', 'category_type']);
        });

        // Data migration: map existing supplier.category string values to category IDs
        $suppliers = DB::table('suppliers')->get();
        foreach ($suppliers as $supplier) {
            if ($supplier->category) {
                $category = DB::table('categories')->where('name', $supplier->category)->first();
                if ($category) {
                    DB::table('suppliers')
                        ->where('id', $supplier->id)
                        ->update([
                            'category_id' => $category->id,
                            'category_type' => 'App\Models\Category',
                        ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'category_type']);
            $table->dropColumn('category_id');
            $table->dropColumn('category_type');
        });
    }
};
