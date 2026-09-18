<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
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

        // Create a client
        $this->client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'phone' => '0987654321',
            'address' => 'Client Address',
        ]);

        // Create products
        $this->product1 = Product::create([
            'name' => 'Test Product 1',
            'sku' => 'TEST001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 100,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        $this->product2 = Product::create([
            'name' => 'Test Product 2',
            'sku' => 'TEST002',
            'sale_price' => 200.00,
            'purchase_price' => 100.00,
            'initial_stock' => 50,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);
    }

    public function test_index_returns_sales_list(): void
    {
        // Create some sales
        Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);

        Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->subDay()->toDateString(),
            'total' => 500.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('sales.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Client');
    }

    public function test_store_creates_sale_successfully(): void
    {
        $saleData = [
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                    'unit_price' => 100.00,
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 1,
                    'unit_price' => 200.00,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('sales.store'), $saleData);

        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseHas('sales', [
            'client_id' => $this->client->id,
            'total' => 400.00,
        ]);
        $this->assertDatabaseHas('clients', [
            'id' => $this->client->id,
            'total_spent' => 400.00,
            'tier' => 'Bronze',
            'status' => 'active',
        ]);
    }

    public function test_store_persists_client_tier_after_sale_reaches_silver(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), [
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 12,
                    'unit_price' => 100.00,
                ],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseHas('sales', [
            'client_id' => $this->client->id,
            'total' => 1200.00,
        ]);
        $this->assertDatabaseHas('clients', [
            'id' => $this->client->id,
            'total_spent' => 1200.00,
            'tier' => 'Silver',
            'status' => 'active',
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        // Test missing client_id
        $response = $this->actingAs($this->user)->post(route('sales.store'), [
            'date' => now()->toDateString(),
            'items' => [],
        ]);

        $response->assertSessionHasErrors('client_id');
    }

    public function test_show_returns_sale_details(): void
    {
        $sale = Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);

        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
            'unit_price' => 100.00,
            'purchase_cost_at_sale' => 50.00,
            'subtotal' => 300.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('sales.show', $sale->id));

        $response->assertStatus(200);
        $response->assertSee('Test Client');
    }

    public function test_store_validates_minimum_items(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), [
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'items' => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    // ====== TASK 2.2: Sale Edit ======

    public function test_edit_returns_sale_edit_view(): void
    {
        $sale = Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
            'unit_price' => 100.00,
            'purchase_cost_at_sale' => 50.00,
            'subtotal' => 300.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('sales.edit', $sale->id));

        $response->assertStatus(200);
        $response->assertSee('Editar Venta');
        $response->assertSee($this->client->name);
        $response->assertSee($this->product1->name);
    }

    // ====== TASK 2.2: Sale Update ======

    public function test_update_modifies_sale_and_restores_stock_on_old_items(): void
    {
        $sale = Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
            'unit_price' => 100.00,
            'purchase_cost_at_sale' => 50.00,
            'subtotal' => 300.00,
        ]);

        // Stock should be reduced before update
        $this->assertEquals(97, $this->product1->fresh()->getStock());

        $response = $this->actingAs($this->user)->put(route('sales.update', $sale->id), [
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 5,
                    'unit_price' => 120.00,
                ],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'client_id' => $this->client->id,
            'total' => 600.00,
        ]);
        $this->assertDatabaseHas('sale_details', [
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 5,
            'unit_price' => 120.00,
            'subtotal' => 600.00,
        ]);
        $this->assertDatabaseCount('sale_details', 1);
        $this->assertEquals(95, $this->product1->fresh()->getStock());
        $this->assertDatabaseHas('clients', [
            'id' => $this->client->id,
            'total_spent' => 600.00,
            'tier' => 'Bronze',
            'status' => 'active',
        ]);
    }

    public function test_update_validation_failure_preserves_sale_details_and_stock(): void
    {
        $sale = Sale::create([
            'client_id' => $this->client->id,
            'date' => '2026-01-10',
            'total' => 300.00,
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
            'unit_price' => 100.00,
            'purchase_cost_at_sale' => 50.00,
            'subtotal' => 300.00,
        ]);
        $stockBefore = $this->product1->fresh()->getStock();

        $response = $this->actingAs($this->user)->put(route('sales.update', $sale->id), [
            'client_id' => $this->client->id,
            'date' => '2026-01-10',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 0,
                    'unit_price' => 120.00,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.quantity');
        $response->assertSessionHasInput('items.0.quantity', 0);
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'client_id' => $this->client->id,
            'date' => '2026-01-10',
            'total' => 300.00,
        ]);
        $this->assertDatabaseHas('sale_details', [
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
            'unit_price' => 100.00,
            'subtotal' => 300.00,
        ]);
        $this->assertEquals($stockBefore, $this->product1->fresh()->getStock());
    }

    // ====== TASK 2.2: Sale Destroy ======

    public function test_destroy_restores_stock_and_deletes_sale(): void
    {
        $sale = Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);
        SaleDetail::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product1->id,
            'quantity' => 3,
            'unit_price' => 100.00,
            'purchase_cost_at_sale' => 50.00,
            'subtotal' => 300.00,
        ]);

        // Stock should be reduced
        $this->assertEquals(97, $this->product1->fresh()->getStock());

        $response = $this->actingAs($this->user)->delete(route('sales.destroy', $sale->id));

        $response->assertRedirect(route('sales.index'));
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
        $this->assertDatabaseCount('sale_details', 0);
        // Stock should be restored (restoreStock increments initial_stock)
        $this->assertEquals(103, $this->product1->fresh()->getStock());
    }
}
