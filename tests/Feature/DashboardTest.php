<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        // Create products with varying stock
        for ($i = 1; $i <= 5; $i++) {
            Product::create([
                'name' => "Product $i",
                'sku' => "SKU00$i",
                'sale_price' => 100.00 * $i,
                'purchase_price' => 50.00 * $i,
                'initial_stock' => $i * 10,
                'supplier_id' => $this->supplier->id,
                'category_id' => 1,
                'category_type' => 'App\Models\Category',
            ]);
        }

        // Create a client
        $this->client = Client::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'phone' => '0987654321',
            'address' => 'Client Address',
        ]);
    }

    // ====== TASK 2.6: Optimized Low Stock Query ======

    public function test_dashboard_low_stock_uses_scope_with_stock(): void
    {
        // Create a product with 0 stock to trigger lowStockProducts count
        $product = Product::create([
            'name' => 'Out of Stock Product',
            'sku' => 'OOS-001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 0,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $queries = DB::getQueryLog();
        // Should not iterate all products with getStock() calls
        // Should use scopeWithStock subquery for lowStockProducts count
        $this->assertLessThanOrEqual(25, count($queries));
    }

    // ====== TASK 2.6: Grouped Monthly Queries ======

    public function test_dashboard_uses_grouped_monthly_queries(): void
    {
        // Create sales and purchases for the last 12 months
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            Sale::create([
                'client_id' => $this->client->id,
                'date' => $date->toDateString(),
                'total' => 100.00 * ($i + 1),
            ]);

            $purchase = Purchase::create([
                'supplier_id' => $this->supplier->id,
                'date' => $date->toDateString(),
                'total' => 50.00 * ($i + 1),
            ]);
            PurchaseDetail::create([
                'purchase_id' => $purchase->id,
                'product_id' => Product::first()->id,
                'quantity' => 2,
                'unit_price' => 25.00,
                'subtotal' => 50.00 * ($i + 1),
            ]);
        }

        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $queries = DB::getQueryLog();
        // Should not have 24+ individual monthly queries (12 sales + 12 purchases)
        // Should use GROUP BY queries instead
        $this->assertLessThanOrEqual(25, count($queries));
    }

    public function test_dashboard_renders_one_trend_chart_with_two_series(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="monthlyTrendChart"', false);
        $response->assertSee('data-labels=', false);
        $response->assertSee('data-sales=', false);
        $response->assertSee('data-purchases=', false);
        $this->assertSame(1, substr_count($response->getContent(), 'id="monthlyTrendChart"'));
    }

    public function test_dashboard_does_not_render_quick_actions_or_header_cta(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertDontSee('Acciones rápidas');
        $response->assertDontSee('Nueva venta');
        $response->assertDontSee('Nueva compra');
        $response->assertDontSee('Nuevo proveedor');
        $response->assertDontSee('dashboard-period');
    }

    public function test_dashboard_renders_range_selector_in_chart_section(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="dashboard-range"', false);
        $response->assertSee('name="range"', false);
        $response->assertSee('Este mes');
        $response->assertSee('Últimos 3 meses');
        $response->assertSee('Últimos 12 meses');
    }

    public function test_dashboard_aligns_monthly_series_with_oldest_to_newest_labels(): void
    {
        $oldestMonth = now()->subMonths(11)->startOfMonth();
        $currentMonth = now()->startOfMonth();

        Sale::create([
            'client_id' => $this->client->id,
            'date' => $oldestMonth,
            'total' => 111.00,
        ]);
        Sale::create([
            'client_id' => $this->client->id,
            'date' => $currentMonth,
            'total' => 999.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $months = $response->viewData('months');
        $salesData = $response->viewData('salesData');

        $this->assertCount(12, $months);
        $this->assertCount(12, $salesData);
        $this->assertSame($oldestMonth->isoFormat('MMM'), $months[0]);
        $this->assertSame($currentMonth->isoFormat('MMM'), $months[11]);
        $this->assertSame(111.0, (float) $salesData[0]);
        $this->assertSame(999.0, (float) $salesData[11]);
    }

    public function test_dashboard_defaults_to_twelve_month_range(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));

        $response->assertStatus(200);
        $this->assertSame('12m', $response->viewData('range'));
        $this->assertSame('Últimos 12 meses', $response->viewData('rangeLabel'));
        $this->assertCount(12, $response->viewData('months'));
        $this->assertCount(12, $response->viewData('salesData'));
        $this->assertCount(12, $response->viewData('purchasesData'));
    }

    public function test_dashboard_range_3m_returns_three_spanish_month_labels(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard') . '?range=3m');

        $response->assertStatus(200);
        $months = $response->viewData('months');

        $this->assertSame('3m', $response->viewData('range'));
        $this->assertSame('Últimos 3 meses', $response->viewData('rangeLabel'));
        $this->assertCount(3, $months);
        $this->assertCount(3, $response->viewData('salesData'));
        $this->assertCount(3, $response->viewData('purchasesData'));
        $this->assertSame(now()->subMonths(2)->isoFormat('MMM'), $months[0]);
        $this->assertSame(now()->isoFormat('MMM'), $months[2]);
    }

    public function test_dashboard_range_1m_returns_single_spanish_month_label(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard') . '?range=1m');

        $response->assertStatus(200);
        $months = $response->viewData('months');

        $this->assertSame('1m', $response->viewData('range'));
        $this->assertSame('Este mes', $response->viewData('rangeLabel'));
        $this->assertCount(1, $months);
        $this->assertCount(1, $response->viewData('salesData'));
        $this->assertCount(1, $response->viewData('purchasesData'));
        $this->assertSame(now()->isoFormat('MMM'), $months[0]);
    }

    public function test_dashboard_invalid_range_falls_back_to_twelve_months(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard') . '?range=bogus');

        $response->assertStatus(200);
        $this->assertSame('12m', $response->viewData('range'));
        $this->assertCount(12, $response->viewData('months'));
    }

    public function test_dashboard_kpis_stay_scoped_to_current_month_regardless_of_range(): void
    {
        Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->subMonths(6)->toDateString(),
            'total' => 5000.00,
        ]);
        Sale::create([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'total' => 123.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('dashboard') . '?range=12m');

        $response->assertStatus(200);
        // KPI block remains current month vs previous month (not range-scoped).
        $this->assertSame(123.0, (float) $response->viewData('salesThisMonth'));
        $this->assertSame(0.0, (float) $response->viewData('salesPreviousMonth'));
    }
}
