<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListFiltersTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;
    protected Category $food;
    protected Category $toys;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Filter User',
            'email' => 'filters@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Filter Supplier',
            'email' => 'filter-supplier@test.com',
            'category_id' => null,
            'category_type' => null,
        ]);

        $this->food = Category::create(['name' => 'Alimento']);
        $this->toys = Category::create(['name' => 'Otros']);

        Product::create([
            'name' => 'Alpha Kibble',
            'sku' => 'FLT-001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 10,
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->food->id,
            'category_type' => Category::class,
        ]);

        Product::create([
            'name' => 'Beta Ball',
            'sku' => 'FLT-002',
            'sale_price' => 200.00,
            'purchase_price' => 80.00,
            'initial_stock' => 5,
            'supplier_id' => $this->supplier->id,
            'category_id' => $this->toys->id,
            'category_type' => Category::class,
        ]);

        $client = Client::create([
            'name' => 'Searchable Client',
            'email' => 'searchable@test.com',
        ]);

        Sale::create([
            'client_id' => $client->id,
            'date' => now()->toDateString(),
            'total' => 150.00,
        ]);
    }

    public function test_products_category_filter_returns_only_matching_category_id_products(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('products.index') . '?category=' . $this->food->id);

        $response->assertStatus(200);
        $products = $response->viewData('products');
        $this->assertCount(1, $products->items());
        $this->assertSame($this->food->id, $products->items()[0]->category_id);
        $response->assertSee('Alpha Kibble');
        $response->assertDontSee('Beta Ball');
    }

    public function test_products_search_filters_by_name(): void
    {
        $response = $this->actingAs($this->user)->get(route('products.index') . '?search=Alpha');

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('products')->items());
        $response->assertSee('Alpha Kibble');
        $response->assertDontSee('Beta Ball');
    }

    public function test_sales_search_filters_by_client_name(): void
    {
        $matching = $this->actingAs($this->user)->get(route('sales.index') . '?search=Searchable');
        $matching->assertStatus(200);
        $this->assertCount(1, $matching->viewData('sales')->items());

        $nonMatching = $this->actingAs($this->user)->get(route('sales.index') . '?search=Nobody');
        $nonMatching->assertStatus(200);
        $this->assertCount(0, $nonMatching->viewData('sales')->items());
    }

    public function test_clients_search_filters_by_name(): void
    {
        $response = $this->actingAs($this->user)->get(route('clients.index') . '?search=Searchable');

        $response->assertStatus(200);
        $this->assertCount(1, $response->viewData('clients')->items());
        $response->assertSee('Searchable Client');
    }

    public function test_products_accepts_valid_sort_and_falls_back_on_invalid_sort(): void
    {
        $valid = $this->actingAs($this->user)->get(route('products.index') . '?sort=sale_price&direction=asc');
        $valid->assertStatus(200);
        $this->assertSame('sale_price', $valid->viewData('sort'));
        $this->assertSame('asc', $valid->viewData('direction'));

        $invalid = $this->actingAs($this->user)->get(route('products.index') . '?sort=password');
        $invalid->assertStatus(200);
        $this->assertSame('created_at', $invalid->viewData('sort'));
    }

    public function test_sales_invalid_sort_falls_back_to_date_default(): void
    {
        $response = $this->actingAs($this->user)->get(route('sales.index') . '?sort=whatever');

        $response->assertStatus(200);
        $this->assertSame('date', $response->viewData('sort'));
    }

    public function test_list_toolbar_renders_direction_hidden_input_and_sort_options(): void
    {
        $response = $this->actingAs($this->user)->get(route('products.index') . '?direction=asc&sort=name');

        $response->assertStatus(200);
        $response->assertSee('name="direction"', false);
        $response->assertSee('value="asc"', false);
        $response->assertSee('Precio de venta');
        $response->assertSee('Precio de compra');
        $response->assertSee('Nombre');
    }
}
