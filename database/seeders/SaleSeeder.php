<?php

namespace Database\Seeders;

use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        $products = Product::all();

        // Create 100 sales over the last 3 months
        for ($i = 0; $i < 100; $i++) {
            $client = $clients->random();
            $date = now()->subDays(rand(1, 90));
            
            $sale = Sale::create([
                'client_id' => $client->id,
                'date' => $date,
                'total' => 0,
            ]);

            $total = 0;
            // Add 1-6 products to each sale
            $selectedProducts = $products->random(rand(1, 6));
            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 5);
                $unitPrice = $product->sale_price;
                $subtotal = $quantity * $unitPrice;
                $total += $subtotal;

                SaleDetail::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'purchase_cost_at_sale' => $product->purchase_price,
                    'subtotal' => $subtotal,
                ]);
            }

            $sale->update(['total' => $total]);
        }

        $salesByClient = Sale::query()
            ->selectRaw('client_id, SUM(total) as total_spent')
            ->groupBy('client_id')
            ->pluck('total_spent', 'client_id');

        foreach ($clients as $client) {
            $client->total_spent = (float) ($salesByClient[$client->id] ?? 0);
            $client->recalculateTier();
        }
    }
}
