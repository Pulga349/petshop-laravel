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
        // FormRequest handles basic validation, now do stock check
        $validated = $request->validated();

        // 🔒 VALIDACIÓN DE STOCK ANTES DE LA TRANSACCIÓN
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

        // TRANSACCIÓN PARA CONSISTENCIA
        try {
            DB::transaction(function () use ($validated) {
                $total = 0;

                // 1. Crear la venta (sin total aún)
                $sale = Sale::create([
                    'client_id' => $validated['client_id'],
                    'date' => $validated['date'],
                    'total' => 0, // se actualiza después
                ]);

                // 2. Crear los detalles Y actualizar stock
                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_id']);
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $total += $subtotal;

                    // Guarda el costo de compra al momento de la venta
                    SaleDetail::create([
                        'sale_id' => $sale->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'purchase_cost_at_sale' => $product->purchase_price,
                        'subtotal' => $subtotal,
                    ]);
                }

                // 3. Actualizar el total de la venta
                $sale->update(['total' => $total]);
            });

            return redirect()->route('sales.index')->with('success', 'Venta registrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la venta: ' . $e->getMessage()])->withInput();
        }
    }
}