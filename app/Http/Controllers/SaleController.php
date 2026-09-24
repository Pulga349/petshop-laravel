<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Client;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $sort = in_array($request->query('sort'), ['date', 'total', 'created_at'], true) ? $request->query('sort') : 'date';
        $direction = $request->query('direction') === 'asc' ? 'asc' : 'desc';
        $sales = Sale::with('client')
            ->when($search !== '', fn ($query) => $query->whereHas('client', fn ($client) => $client->where('name', 'like', "%{$search}%"))->orWhere('sales.id', $search))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();
        return view('sales.index', compact('sales', 'search', 'sort', 'direction'));
    }

    public function create(): RedirectResponse
    {
        // The POS terminal moved to its own route; keep old links working.
        return redirect()->route('pos');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['client', 'details.product']);
        return view('sales.show', compact('sale'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $stockErrors = [];
        foreach ($validated['items'] as $index => $item) {
            $product = Product::find($item['product_id']);
            $stockActual = $product->getStock();

            if ($stockActual < $item['quantity']) {
                $stockErrors[] = "Stock insuficiente para '{$product->name}': disponible {$stockActual}, solicitado {$item['quantity']}";
            }
        }

        if (!empty($stockErrors)) {
            return back()->withErrors(['items' => $stockErrors])->withInput();
        }

        // Server-authoritative totals, computed before any write so an insufficient
        // cash payment never creates a sale (prices include VAT; discount by tier).
        $subtotal = 0;
        foreach ($validated['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }

        $client = Client::findOrFail($validated['client_id']);
        $discountPercent = (float) Setting::get('discount_' . strtolower($client->tier ?? 'Bronze'), 0);
        $discountAmount = round($subtotal * $discountPercent / 100, 2);
        $total = round($subtotal - $discountAmount, 2);
        $ivaRate = (float) Setting::get('iva_rate', 21);
        // VAT-included prices: IVA is the derived portion of the final total.
        $ivaAmount = round($total * $ivaRate / (100 + $ivaRate), 2);
        $paymentMethod = $validated['payment_method'] ?? null;
        $amountPaid = isset($validated['amount_paid']) ? round((float) $validated['amount_paid'], 2) : null;

        if ($paymentMethod === 'efectivo' && ($amountPaid ?? 0) < $total) {
            return back()->withErrors(['amount_paid' => 'El monto recibido es insuficiente.'])->withInput();
        }

        try {
            DB::transaction(function () use ($validated, $client, $total, $discountAmount, $ivaAmount, $paymentMethod, $amountPaid) {
                $sale = Sale::create([
                    'client_id' => $validated['client_id'],
                    'date' => $validated['date'],
                    'total' => $total,
                    'discount_amount' => $discountAmount,
                    'iva_amount' => $ivaAmount,
                    'payment_method' => $paymentMethod,
                    'amount_paid' => $amountPaid,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'purchase_cost_at_sale' => $product->purchase_price,
                        'subtotal' => $item['quantity'] * $item['unit_price'],
                    ]);
                }

                $client->total_spent = Sale::where('client_id', $client->id)->sum('total');
                $client->recalculateTier();
            });

            return redirect()->route('sales.index')->with('success', 'Venta registrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Sale $sale): View
    {
        $sale->load(['client', 'details.product']);
        $clients = Client::all();
        $products = Product::with('supplier')->get();
        return view('sales.edit', compact('sale', 'clients', 'products'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $sale) {
                // Rebuild details only when items were submitted; otherwise keep the
                // existing ones (UpdateSaleRequest allows items to be omitted).
                if (!empty($validated['items'])) {
                    $sale->details()->delete();

                    $subtotal = 0;
                    foreach ($validated['items'] as $item) {
                        $product = Product::findOrFail($item['product_id']);
                        $lineSubtotal = $item['quantity'] * $item['unit_price'];
                        $subtotal += $lineSubtotal;

                        SaleDetail::create([
                            'sale_id' => $sale->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'purchase_cost_at_sale' => $product->purchase_price,
                            'subtotal' => $lineSubtotal,
                        ]);
                    }
                } else {
                    $subtotal = (float) $sale->details()->sum('subtotal');
                }

                // Discount/VAT/total follow the effective client tier (prices include VAT)
                $effectiveClientId = $validated['client_id'] ?? $sale->client_id;
                $client = Client::findOrFail($effectiveClientId);
                $discountPercent = (float) Setting::get('discount_' . strtolower($client->tier ?? 'Bronze'), 0);
                $discountAmount = round($subtotal * $discountPercent / 100, 2);
                $total = round($subtotal - $discountAmount, 2);
                $ivaRate = (float) Setting::get('iva_rate', 21);
                $ivaAmount = round($total * $ivaRate / (100 + $ivaRate), 2);

                $update = [
                    'total' => $total,
                    'discount_amount' => $discountAmount,
                    'iva_amount' => $ivaAmount,
                ];

                if (array_key_exists('client_id', $validated) && $validated['client_id'] !== null) {
                    $update['client_id'] = $validated['client_id'];
                }
                if (array_key_exists('date', $validated) && $validated['date'] !== null) {
                    $update['date'] = $validated['date'];
                }
                if (array_key_exists('payment_method', $validated)) {
                    $update['payment_method'] = $validated['payment_method'];
                }
                if (array_key_exists('amount_paid', $validated)) {
                    $update['amount_paid'] = $validated['amount_paid'] !== null
                        ? round((float) $validated['amount_paid'], 2)
                        : null;
                }

                $sale->update($update);

                // Recalculate client tier
                $client->total_spent = Sale::where('client_id', $client->id)->sum('total');
                $client->recalculateTier();
            });

            return redirect()->route('sales.index')->with('success', 'Venta actualizada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar la venta: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Sale $sale): RedirectResponse
    {
        try {
            DB::transaction(function () use ($sale) {
                $sale->restoreStock();
                $sale->details()->delete();
                $sale->delete();
            });

            return redirect()->route('sales.index')->with('success', 'Venta eliminada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la venta: ' . $e->getMessage()])->withInput();
        }
    }
}
