<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $purchasePrice = fake()->randomFloat(2, 10, 100);
        $salePrice = $purchasePrice * fake()->randomFloat(2, 1.3, 2.5);

        return [
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'purchase_price' => $purchasePrice,
            'sale_price' => $salePrice,
            'supplier_id' => Supplier::factory(),
        ];
    }
}