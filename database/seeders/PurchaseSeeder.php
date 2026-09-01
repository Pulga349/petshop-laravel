<?php

namespace Database\Seeders;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products = Product::all();

        // Create 40 purchases over the last 6 months
        for ($i = 0; $i < 40; $i++) {
            $supplier = $suppliers->random();
            $date = now()->subDays(rand(1, 180));
            
            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'date' => $date,
            ]);

            // Add 3-8 products to each purchase
            $selectedProducts = $products->random(rand(3, 8));
            foreach ($selectedProducts as $product) {
                $quantity = rand(5, 50);
                $unitPrice = $product->purchase_price;
                PurchaseDetail::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $quantity * $unitPrice,
                ]);
            }
        }
    }
}