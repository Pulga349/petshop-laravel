<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalePaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Supplier $supplier;
    protected Client $client;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Payment Test User',
            'email' => 'payment@test.com',
            'password' => bcrypt('password'),
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Payment Supplier',
            'email' => 'payment@supplier.com',
            'phone' => '1234567890',
            'address' => 'Test Address',
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        $this->client = Client::create([
            'name' => 'Payment Client',
            'email' => 'payment@client.com',
            'phone' => '0987654321',
            'address' => 'Client Address',
        ]);

        $this->product1 = Product::create([
            'name' => 'Payment Product 1',
            'sku' => 'PAY001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 100,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        $this->product2 = Product::create([
            'name' => 'Payment Product 2',
            'sku' => 'PAY002',
            'sale_price' => 200.00,
            'purchase_price' => 100.00,
            'initial_stock' => 50,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);
    }

    /**
     * Default payload: Bronze client (0% discount), subtotal 400, total 400.
     */
    private function validSalePayload(array $overrides = []): array
    {
        return array_merge([
            'client_id' => $this->client->id,
            'date' => now()->toDateString(),
            'payment_method' => 'efectivo',
            'amount_paid' => 500.00,
            'items' => [
                ['product_id' => $this->product1->id, 'quantity' => 2, 'unit_price' => 100.00],
                ['product_id' => $this->product2->id, 'quantity' => 1, 'unit_price' => 200.00],
            ],
        ], $overrides);
    }

    public function test_store_with_cash_payment_persists_payment_discount_and_iva(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), $this->validSalePayload());

        $response->assertRedirect(route('sales.index'));
        // total 400 (Bronze 0%), iva = 400 * 21/121 = 69.42
        $this->assertDatabaseHas('sales', [
            'client_id' => $this->client->id,
            'payment_method' => 'efectivo',
            'amount_paid' => 500.00,
            'discount_amount' => 0.00,
            'iva_amount' => 69.42,
            'total' => 400.00,
        ]);
    }

    public function test_store_applies_silver_tier_discount(): void
    {
        $silverClient = Client::create([
            'name' => 'Silver Client',
            'email' => 'silver@client.com',
            'phone' => '0987654322',
            'address' => 'Silver Address',
            'tier' => 'Silver',
        ]);

        // subtotal 1000, Silver 5% => discount 50, total 950
        $response = $this->actingAs($this->user)->post(route('sales.store'), [
            'client_id' => $silverClient->id,
            'date' => now()->toDateString(),
            'payment_method' => 'efectivo',
            'amount_paid' => 1000.00,
            'items' => [
                ['product_id' => $this->product1->id, 'quantity' => 10, 'unit_price' => 100.00],
            ],
        ]);

        $response->assertRedirect(route('sales.index'));

        $sale = Sale::where('client_id', $silverClient->id)->firstOrFail();
        $this->assertGreaterThan(0, (float) $sale->discount_amount);
        $this->assertEqualsWithDelta(50.00, (float) $sale->discount_amount, 0.01);
        $this->assertEqualsWithDelta(950.00, (float) $sale->total, 0.01);
        // iva = 950 * 21/121 = 164.88
        $this->assertDatabaseHas('sales', [
            'discount_amount' => 50.00,
            'total' => 950.00,
            'iva_amount' => 164.88,
        ]);
    }

    public function test_store_rejects_insufficient_cash_payment(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), $this->validSalePayload([
            'amount_paid' => 100.00,
        ]));

        $response->assertSessionHasErrors('amount_paid');
        $response->assertSessionHasInput('payment_method', 'efectivo');
        $this->assertDatabaseCount('sales', 0);
        $this->assertEquals(100, $this->product1->fresh()->getStock());
    }

    public function test_store_requires_amount_paid_for_cash_payments(): void
    {
        $payload = $this->validSalePayload();
        unset($payload['amount_paid']);

        $response = $this->actingAs($this->user)->post(route('sales.store'), $payload);

        $response->assertSessionHasErrors('amount_paid');
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_store_rejects_invalid_payment_method(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), $this->validSalePayload([
            'payment_method' => 'bitcoin',
        ]));

        $response->assertSessionHasErrors('payment_method');
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_store_derives_iva_as_vat_included_portion(): void
    {
        $response = $this->actingAs($this->user)->post(route('sales.store'), $this->validSalePayload([
            'amount_paid' => 300.00,
            'items' => [
                ['product_id' => $this->product1->id, 'quantity' => 3, 'unit_price' => 100.00],
            ],
        ]));

        $response->assertRedirect(route('sales.index'));
        // total 300, iva = 300 * 21/121 = 52.07
        $this->assertDatabaseHas('sales', [
            'total' => 300.00,
            'discount_amount' => 0.00,
            'iva_amount' => 52.07,
        ]);
    }

    public function test_store_uses_runtime_configured_iva_rate(): void
    {
        Setting::set('iva_rate', '10');

        $response = $this->actingAs($this->user)->post(route('sales.store'), $this->validSalePayload([
            'amount_paid' => 300.00,
            'items' => [
                ['product_id' => $this->product1->id, 'quantity' => 3, 'unit_price' => 100.00],
            ],
        ]));

        $response->assertRedirect(route('sales.index'));
        // total 300, iva = 300 * 10/110 = 27.27
        $this->assertDatabaseHas('sales', [
            'total' => 300.00,
            'iva_amount' => 27.27,
        ]);
    }
}
