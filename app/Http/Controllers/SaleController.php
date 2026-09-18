<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SaleController extends Controller
{
    public function index(): View
    {
        $sales = Sale::with('client')->paginate(15);
        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $clients = Client::all();
        $products = Product::with('supplier')->get();
        return view('sales.create', compact('clients', 'products'));
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

        try {
            DB::transaction(function () use ($validated) {
                $total = 0;

                $sale = Sale::create([
                    'client_id' => $validated['client_id'],
                    'date' => $validated['date'],
                    'total' => 0,
                ]);

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $total += $subtotal;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'purchase_cost_at_sale' => $product->purchase_price,
                        'subtotal' => $subtotal,
                    ]);
                }

                $sale->update(['total' => $total]);

                $client = $sale->client;
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
                // Delete old details
                $sale->details()->delete();

                // Calculate new total
                $total = 0;
                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $total += $subtotal;

                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'purchase_cost_at_sale' => $product->purchase_price,
                        'subtotal' => $subtotal,
                    ]);
                }

                // Update sale total
                $sale->update(['total' => $total]);

                // Recalculate client tier
                $client = $sale->client;
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