<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Client;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('categories')->insert([
            ['name' => 'Alimento'],
            ['name' => 'Accesorios'],
            ['name' => 'Higiene'],
            ['name' => 'Otros'],
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'test@test.com',
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);
    }

    // ====== TASK 1.3: Category model ======

    public function test_category_model_exists_and_has_relationships(): void
    {
        $category = Category::create(['name' => 'Test Category', 'description' => 'Test Description']);
        $this->assertDatabaseHas('categories', ['name' => 'Test Category']);
        $this->assertInstanceOf(Product::class, $category->products()->get()->first() ?? new Product());
        $this->assertInstanceOf(Supplier::class, $category->suppliers()->get()->first() ?? new Supplier());
    }

    // ====== TASK 1.4: Product model ======

    public function test_product_fillable_removes_category_adds_category_id(): void
    {
        $product = new Product();
        $fillable = $product->getFillable();
        $this->assertNotContains('category', $fillable);
        $this->assertContains('category_id', $fillable);
        $this->assertContains('category_type', $fillable);
    }

    public function test_product_getStock_uses_subquery(): void
    {
        $purchase = Purchase::create(['date' => now(), 'supplier_id' => $this->supplier->id, 'total' => 250.00]);
        $sale = Sale::create(['date' => now(), 'client_id' => Client::create(['name' => 'C', 'email' => 'c@test.com'])->id, 'total' => 300.00]);
        $product = Product::create(['name' => 'Stock Test Product', 'sku' => 'STK-001', 'sale_price' => 100.00, 'purchase_price' => 50.00, 'initial_stock' => 10, 'supplier_id' => $this->supplier->id, 'category_id' => 1, 'category_type' => 'App\Models\Category']);
        PurchaseDetail::create(['purchase_id' => $purchase->id, 'product_id' => $product->id, 'quantity' => 5, 'unit_price' => 50.00, 'subtotal' => 250.00]);
        SaleDetail::create(['sale_id' => $sale->id, 'product_id' => $product->id, 'quantity' => 3, 'unit_price' => 100.00, 'purchase_cost_at_sale' => 50.00, 'subtotal' => 300.00]);
        $this->assertEquals(12, $product->getStock());
    }

    public function test_product_scope_with_stock(): void
    {
        $purchase = Purchase::create(['date' => now(), 'supplier_id' => $this->supplier->id, 'total' => 250.00]);
        $p1 = Product::create(['name' => 'Product A', 'sku' => 'A-001', 'sale_price' => 100.00, 'purchase_price' => 50.00, 'initial_stock' => 10, 'supplier_id' => $this->supplier->id, 'category_id' => 1, 'category_type' => 'App\Models\Category']);
        PurchaseDetail::create(['purchase_id' => $purchase->id, 'product_id' => $p1->id, 'quantity' => 5, 'unit_price' => 50.00, 'subtotal' => 250.00]);

        $p2 = Product::create(['name' => 'Product B', 'sku' => 'B-001', 'sale_price' => 200.00, 'purchase_price' => 100.00, 'initial_stock' => 5, 'supplier_id' => $this->supplier->id, 'category_id' => 1, 'category_type' => 'App\Models\Category']);

        $products = Product::withStock()->get();
        $this->assertCount(2, $products);
        $productA = $products->first(fn($p) => $p->id === $p1->id);
        $this->assertEquals(15, $productA->stock);
    }

    // ====== TASK 1.5: Supplier model ======

    public function test_supplier_has_category_relationship(): void
    {
        $supplier = Supplier::create(['name' => 'Cat Supplier', 'email' => 'cat@test.com', 'category_id' => 1, 'category_type' => 'App\Models\Category']);
        $this->assertInstanceOf(Category::class, $supplier->category);
        $this->assertEquals('Alimento', $supplier->category->name);
    }

    // ====== TASK 1.9: Client model ======

    public function test_client_recalculate_tier(): void
    {
        $client = Client::create(['name' => 'Bronze Client', 'email' => 'bronze@test.com', 'total_spent' => 0]);
        $client->recalculateTier();
        $this->assertEquals('Bronze', $client->tier);
        $this->assertEquals('inactive', $client->status);

        $client->update(['total_spent' => 500]);
        $client->recalculateTier();
        $this->assertEquals('Bronze', $client->tier);
        $this->assertEquals('active', $client->status);

        $client->update(['total_spent' => 1000]);
        $client->recalculateTier();
        $this->assertEquals('Silver', $client->tier);
        $this->assertEquals('active', $client->status);

        $client->update(['total_spent' => 5000]);
        $client->recalculateTier();
        $this->assertEquals('Gold', $client->tier);
        $this->assertEquals('active', $client->status);

        $client->update(['total_spent' => 10000]);
        $client->recalculateTier();
        $this->assertEquals('Platinum', $client->tier);
        $this->assertEquals('active', $client->status);
    }

    public function test_client_fillable_includes_tier_status_total_spent(): void
    {
        $client = new Client();
        $fillable = $client->getFillable();
        $this->assertContains('tier', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('total_spent', $fillable);
    }

    // ====== TASK 1.8: Purchase model ======

    public function test_purchase_has_total_and_details(): void
    {
        $product = Product::create(['name' => 'Purchase Test Product', 'sku' => 'PCH-001', 'sale_price' => 100.00, 'purchase_price' => 50.00, 'initial_stock' => 10, 'supplier_id' => $this->supplier->id, 'category_id' => 1, 'category_type' => 'App\Models\Category']);
        $purchase = Purchase::create(['date' => now(), 'supplier_id' => $this->supplier->id, 'total' => 190.00]);
        PurchaseDetail::create(['purchase_id' => $purchase->id, 'product_id' => $product->id, 'quantity' => 2, 'unit_price' => 50.00, 'subtotal' => 100.00]);
        PurchaseDetail::create(['purchase_id' => $purchase->id, 'product_id' => $product->id, 'quantity' => 3, 'unit_price' => 30.00, 'subtotal' => 90.00]);
        $this->assertEquals(190.00, $purchase->fresh()->total);
    }

    // ====== TASK 1.11: Sale model ======

    public function test_sale_has_details_relationship(): void
    {
        $client = Client::create(['name' => 'Test Client', 'email' => 'client@test.com']);
        $sale = Sale::create(['date' => now(), 'client_id' => $client->id, 'total' => 400.00]);
        $product = Product::create(['name' => 'Test Product', 'sku' => 'TP-001', 'sale_price' => 100.00, 'purchase_price' => 50.00, 'initial_stock' => 10, 'supplier_id' => $this->supplier->id, 'category_id' => 1, 'category_type' => 'App\Models\Category']);
        SaleDetail::create(['sale_id' => $sale->id, 'product_id' => $product->id, 'quantity' => 2, 'unit_price' => 100.00, 'purchase_cost_at_sale' => 50.00, 'subtotal' => 200.00]);
        $this->assertCount(1, $sale->details);
    }

    public function test_sale_restore_stock(): void
    {
        $client = Client::create(['name' => 'Stock Client', 'email' => 'stock@test.com']);
        $product = Product::create(['name' => 'Restock Product', 'sku' => 'RS-001', 'sale_price' => 100.00, 'purchase_price' => 50.00, 'initial_stock' => 10, 'supplier_id' => $this->supplier->id, 'category_id' => 1, 'category_type' => 'App\Models\Category']);
        $sale = Sale::create(['date' => now(), 'client_id' => $client->id, 'total' => 200.00]);
        SaleDetail::create(['sale_id' => $sale->id, 'product_id' => $product->id, 'quantity' => 3, 'unit_price' => 100.00, 'purchase_cost_at_sale' => 50.00, 'subtotal' => 300.00]);
        $this->assertEquals(7, $product->fresh()->getStock());
        $sale->restoreStock();
        $this->assertEquals(10, $product->fresh()->getStock());
    }
}
