<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowViewTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Product $product;
    protected Supplier $supplier;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'showtest@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Show Supplier',
            'email' => 'show@supplier.com',
            'phone' => '1234567890',
            'address' => 'Show Address',
            'category' => 'Alimento',
        ]);

        $this->product = Product::create([
            'name' => 'Show Product',
            'sku' => 'SHOW001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 10,
            'supplier_id' => $this->supplier->id,
            'category' => 'Nutrition',
        ]);

        $this->client = Client::create([
            'name' => 'Show Client',
            'email' => 'show@client.com',
            'phone' => '0987654321',
            'address' => 'Show Client Address',
        ]);
    }

    public function test_product_show_returns_200(): void
    {
        $response = $this->actingAs($this->user)->get(route('products.show', $this->product));

        $response->assertStatus(200);
        $response->assertSee('Show Product');
        $response->assertSee('Show Supplier');
    }

    public function test_supplier_show_returns_200(): void
    {
        $response = $this->actingAs($this->user)->get(route('suppliers.show', $this->supplier));

        $response->assertStatus(200);
        $response->assertSee('Show Supplier');
        $response->assertSee('Alimento');
    }

    public function test_client_show_returns_200(): void
    {
        $response = $this->actingAs($this->user)->get(route('clients.show', $this->client));

        $response->assertStatus(200);
        $response->assertSee('Show Client');
    }

    public function test_purchase_show_returns_200(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
        ]);

        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'unit_price' => 50.00,
            'subtotal' => 250.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('purchases.show', $purchase));

        $response->assertStatus(200);
        $response->assertSee('Show Product');
    }

    public function test_product_show_returns_404_for_missing(): void
    {
        $response = $this->actingAs($this->user)->get('/products/99999');
        $response->assertStatus(404);
    }
}
