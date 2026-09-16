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
}
