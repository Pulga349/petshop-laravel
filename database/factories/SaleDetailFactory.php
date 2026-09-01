<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleDetailFactory extends Factory
{
    protected $model = SaleDetail::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 10, 300);
        $purchaseCost = fake()->randomFloat(2, 5, 100);
        $subtotal = $quantity * $unitPrice;

        return [
            'sale_id' => Sale::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'purchase_cost_at_sale' => $purchaseCost,
            'subtotal' => $subtotal,
        ];
    }
}