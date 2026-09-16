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
        Schema::table('products', function (Blueprint $table) {
            // Add polymorphic columns
            $table->unsignedBigInteger('category_id')->nullable()->after('sku');
            $table->string('category_type')->nullable()->after('category_id');
            $table->index(['category_id', 'category_type']);
        });

        // Data migration: map existing product.category string values to category IDs
        $categoryMap = [
            'Nutrition' => 'Alimento',
            'Accessories' => 'Accesorios',
            'Hygiene' => 'Higiene',
        ];

        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $categoryName = $categoryMap[$product->category] ?? 'Otros';
            $category = DB::table('categories')->where('name', $categoryName)->first();
            if ($category) {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update([
                        'category_id' => $category->id,
                        'category_type' => 'App\Models\Category',
                    ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['category_id', 'category_type']);
            $table->dropColumn('category_id');
            $table->dropColumn('category_type');
        });
    }
};
