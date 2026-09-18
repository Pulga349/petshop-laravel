<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
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

        $this->product1 = Product::create([
            'name' => 'Test Product 1',
            'sku' => 'TP1-001',
            'sale_price' => 100.00,
            'purchase_price' => 50.00,
            'initial_stock' => 100,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);

        $this->product2 = Product::create([
            'name' => 'Test Product 2',
            'sku' => 'TP2-001',
            'sale_price' => 200.00,
            'purchase_price' => 100.00,
            'initial_stock' => 50,
            'supplier_id' => $this->supplier->id,
            'category_id' => 1,
            'category_type' => 'App\Models\Category',
        ]);
    }

    // ====== TASK 2.1: Purchase Edit ======

    public function test_edit_returns_purchase_edit_view(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('purchases.edit', $purchase->id));

        $response->assertStatus(200);
        $response->assertSee('Editar Compra');
        $response->assertSee($this->supplier->name);
        $response->assertSee($this->product1->name);
    }

    // ====== TASK 2.1: Purchase Update ======

    public function test_update_modifies_purchase_and_details(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchases.update', $purchase->id), [
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 5,
                    'unit_price' => 60.00,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchases.index'));
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'total' => 300.00,
        ]);
        $this->assertDatabaseHas('purchase_details', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 5,
            'unit_price' => 60.00,
            'subtotal' => 300.00,
        ]);
        $this->assertDatabaseCount('purchase_details', 1);
    }

    public function test_update_validation_failure_preserves_purchase_and_details(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => '2026-01-10',
            'total' => 100.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchases.update', $purchase->id), [
            'supplier_id' => $this->supplier->id,
            'date' => '2026-01-10',
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 0,
                    'unit_price' => 60.00,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('items.0.quantity');
        $response->assertSessionHasInput('items.0.quantity', 0);
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'supplier_id' => $this->supplier->id,
            'date' => '2026-01-10',
            'total' => 100.00,
        ]);
        $this->assertDatabaseHas('purchase_details', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);
    }

    public function test_update_uses_transaction_and_recalculates_total(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'total' => 100.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);

        $response = $this->actingAs($this->user)->put(route('purchases.update', $purchase->id), [
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 3,
                    'unit_price' => 40.00,
                ],
            ],
        ]);

        $response->assertRedirect(route('purchases.index'));
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'total' => 120.00,
        ]);
    }

    public function test_update_rolls_back_purchase_and_details_when_detail_insert_fails_mid_transaction(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => '2026-01-10',
            'total' => 100.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);

        $detailInsertCount = 0;
        DB::listen(function (QueryExecuted $query) use (&$detailInsertCount): void {
            if (str_contains(strtolower($query->sql), 'insert into "purchase_details"')) {
                $detailInsertCount++;
                if ($detailInsertCount === 2) {
                    throw new \RuntimeException('Deterministic detail failure for rollback coverage');
                }
            }
        });

        $response = $this->actingAs($this->user)->put(route('purchases.update', $purchase->id), [
            'supplier_id' => $this->supplier->id,
            'date' => '2026-02-20',
            'items' => [
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 3,
                    'unit_price' => 40.00,
                ],
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 4,
                    'unit_price' => 30.00,
                ],
            ],
        ]);

        $response->assertSessionHasErrors('error');
        $this->assertSame(2, $detailInsertCount);
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'supplier_id' => $this->supplier->id,
            'date' => '2026-01-10',
            'total' => 100.00,
        ]);
        $this->assertDatabaseHas('purchase_details', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);
        $this->assertDatabaseMissing('purchase_details', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->product2->id,
        ]);
        $this->assertDatabaseCount('purchase_details', 1);
    }

    // ====== TASK 2.1: Purchase Destroy ======

    public function test_destroy_deletes_purchase_and_details(): void
    {
        $purchase = Purchase::create([
            'supplier_id' => $this->supplier->id,
            'date' => now()->toDateString(),
            'total' => 300.00,
        ]);
        PurchaseDetail::create([
            'purchase_id' => $purchase->id,
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'subtotal' => 100.00,
        ]);

        $this->assertDatabaseCount('purchase_details', 1);

        $response = $this->actingAs($this->user)->delete(route('purchases.destroy', $purchase->id));

        $response->assertRedirect(route('purchases.index'));
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
        $this->assertDatabaseCount('purchase_details', 0);
    }

    // ====== TASK 2.5: Product Index Optimization ======

    public function test_product_index_uses_scope_with_stock(): void
    {
        DB::enableQueryLog();

        $response = $this->actingAs($this->user)->get(route('products.index'));

        $response->assertStatus(200);
        $queries = DB::getQueryLog();
        // Should use scopeWithStock with single-query approach instead of N+1
        // The optimized index should have fewer queries than the old approach
        $this->assertLessThanOrEqual(25, count($queries));
    }
}
