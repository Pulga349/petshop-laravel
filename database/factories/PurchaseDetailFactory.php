<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class PurchaseDetailFactory extends Factory
{
    protected $model = PurchaseDetail::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(5, 50);
        $unitPrice = fake()->randomFloat(2, 5, 200);

        return [
            'purchase_id' => Purchase::factory(),
            'product_id' => Product::factory(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $quantity * $unitPrice,
        ];
    }
}