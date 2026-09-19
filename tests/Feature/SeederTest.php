<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ====== TASK 3.5: Category Polymorphic Data ======

    public function test_categories_are_seeded_correctly(): void
    {
        $this->assertDatabaseCount('categories', 4);
        $this->assertDatabaseHas('categories', ['name' => 'Alimento']);
        $this->assertDatabaseHas('categories', ['name' => 'Accesorios']);
        $this->assertDatabaseHas('categories', ['name' => 'Higiene']);
        $this->assertDatabaseHas('categories', ['name' => 'Otros']);
    }

    public function test_suppliers_have_polymorphic_category_data(): void
    {
        $suppliers = Supplier::all();
        $this->assertGreaterThan(0, $suppliers->count());

        foreach ($suppliers as $supplier) {
            $this->assertNotNull($supplier->category_id);
            $this->assertNotNull($supplier->category_type);
            $this->assertEquals('App\Models\Category', $supplier->category_type);
            $this->assertInstanceOf(Category::class, $supplier->category);
        }
    }

    public function test_products_have_polymorphic_category_data(): void
    {
        $products = Product::all();
        $this->assertGreaterThan(0, $products->count());

        foreach ($products as $product) {
            $this->assertNotNull($product->category_id);
            $this->assertNotNull($product->category_type);
            $this->assertEquals('App\Models\Category', $product->category_type);
            $this->assertInstanceOf(Category::class, $product->category);
        }
    }

    public function test_products_are_not_using_string_category_field(): void
    {
        $product = Product::first();
        $fillable = $product->getFillable();
        $this->assertContains('category_id', $fillable);
        $this->assertContains('category_type', $fillable);
    }

    // ====== TASK 3.5: Client Tier Data ======

    public function test_clients_have_tier_status_and_total_spent(): void
    {
        $clients = Client::all();
        $this->assertGreaterThan(0, $clients->count());

        foreach ($clients as $client) {
            $this->assertNotNull($client->tier);
            $this->assertNotNull($client->status);
            $this->assertNotNull($client->total_spent);
            $this->assertContains($client->tier, ['Bronze', 'Silver', 'Gold', 'Platinum']);
            $this->assertContains($client->status, ['active', 'inactive']);
        }
    }

    public function test_client_tiers_match_thresholds(): void
    {
        $client = Client::create([
            'name' => 'Tier Test Client',
            'email' => 'tier@test.com',
            'total_spent' => 7500,
        ]);
        $client->recalculateTier();

        $this->assertEquals('Gold', $client->tier);
        $this->assertEquals('active', $client->status);
    }

    public function test_clients_with_zero_total_spent_are_inactive(): void
    {
        $inactiveClients = Client::where('status', 'inactive')->get();
        foreach ($inactiveClients as $client) {
            $this->assertEquals(0, $client->total_spent);
            $this->assertEquals('Bronze', $client->tier);
        }
    }

    public function test_seeded_client_totals_and_tiers_match_seeded_sales(): void
    {
        foreach (Client::all() as $client) {
            $expectedTotal = round((float) Sale::where('client_id', $client->id)->sum('total'), 2);
            $expectedTier = match (true) {
                $expectedTotal >= 10000 => 'Platinum',
                $expectedTotal >= 5000 => 'Gold',
                $expectedTotal >= 1000 => 'Silver',
                default => 'Bronze',
            };
            $expectedStatus = $expectedTotal > 0 ? 'active' : 'inactive';

            $this->assertSame($expectedTotal, round((float) $client->total_spent, 2));
            $this->assertSame($expectedTier, $client->tier);
            $this->assertSame($expectedStatus, $client->status);
        }
    }
}
