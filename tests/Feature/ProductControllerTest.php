<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Client;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication (following SaleControllerTest's ::create() pattern)
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        // Create a supplier for products
        $this->supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        // Create 5 products linked to the supplier
        for ($i = 1; $i <= 5; $i++) {
            Product::create([
                'name' => "Product $i",
                'sku' => "SKU00$i",
                'sale_price' => 100.00,
                'purchase_price' => 50.00,
                'initial_stock' => 100,
                'supplier_id' => $this->supplier->id,
                'category_id' => 1,
                'category_type' => 'App\Models\Category',
            ]);
        }
    }

    /**
     * Product index uses eager loading to prevent N+1 queries.
     *
     * Expected ≤ 25 queries across all layers:
     * Controller (5):
     *   1. Pagination count    — SELECT COUNT(*) FROM products
     *   2. Paginated products  — SELECT * FROM products LIMIT 15
     *   3. Eager suppliers      — SELECT * FROM suppliers WHERE id IN (…)
     *   4. KPI total count     — SELECT COUNT(*) FROM products
     *   5. KPI all products    — SELECT * FROM products
     *
     * View getStock() (10 = 2 per product × 5 products):
     *   6,8,10,12,14  — SELECT COALESCE(SUM(quantity),0) FROM purchase_details WHERE product_id = ?
     *   7,9,11,13,15  — SELECT COALESCE(SUM(quantity),0) FROM sale_details   WHERE product_id = ?
     *
     * Framework (~6): session read/write (database driver), auth user load, CSRF
     *   16-21          — sessions, users, migrations table checks
     *
     * The ≤ 25 bound tolerates minor framework query churn while still catching
     * an N+1 regression (which would add 1 query per displayed product per lazy
     * relationship, quickly spiking the count).
     */
    public function test_product_index_uses_eager_loading(): void
    {
        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Product 1');
        $products = $response->viewData('products');
        $this->assertCount(5, $products->items());
        $this->assertTrue($products->items()[0]->relationLoaded('supplier'));
        $this->assertSame($this->supplier->id, $products->items()[0]->supplier->id);
        $this->assertSame($this->supplier->name, $products->items()[0]->supplier->name);
        $this->assertLessThanOrEqual(25, count(DB::getQueryLog()));
    }

    public function test_product_index_exposes_kpi_values_from_scoped_products(): void
    {
        Product::create([
            'name' => 'Out of Stock Product',
            'sku' => 'OOS-001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 0,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\\Models\\Category',
        ]);

        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $this->assertSame(6, $response->viewData('totalProducts'));
        $this->assertSame(1, $response->viewData('outOfStockCount'));
        $this->assertSame(25000.0, (float) $response->viewData('totalInventoryValue'));
    }

    public function test_product_index_exposes_computed_stock_on_paginated_product_model(): void
    {
        $product = Product::where('sku', 'SKU001')->firstOrFail();
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'total' => 1250.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $product->id,
            'quantity' => 25,
            'unit_price' => 50.00,
            'subtotal' => 1250.00,
        ]);

        $client = Client::create([
            'name' => 'Stock Test Client',
            'email' => 'stock-test@test.com',
        ]);
        $sale = Sale::create([
            'client_id' => $client->id,
            'date' => now()->toDateString(),
            'total' => 1600.00,
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 16,
            'unit_price' => 100.00,
            'purchase_cost_at_sale' => 50.00,
            'subtotal' => 1600.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertCount(5, $products->items());
        $this->assertSame(109, (int) $products->items()[0]->stock);
    }

    public function test_product_index_exposes_initial_stock_when_product_has_no_movements(): void
    {
        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertSame(100, (int) $products->items()[1]->stock);
    }
}
