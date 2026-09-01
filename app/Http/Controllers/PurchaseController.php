<?php
namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with('supplier')
            ->select('purchases.*')
            ->selectSub(
                PurchaseDetail::selectRaw('SUM(subtotal) as total')
                    ->whereColumn('purchase_id', 'purchases.id'),
                'total_computed'
            )
            ->paginate(15);

        return view('purchases.index', compact('purchases'));
    }

    public function create(): View
    {
        $suppliers = Supplier::all();
        $products = Product::with('supplier')->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function show(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'details.product']);
        return view('purchases.show', compact('purchase'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        // FormRequest handles basic validation
        $validated = $request->validated();

        // VALIDACIÓN DE STOCK NO ES NECESARIA EN COMPRAS (solo suma)
        // Pero sí validamos que los productos pertenezcan al proveedor
        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->supplier_id != $validated['supplier_id']) {
                return back()->withErrors([
                    'items' => "El producto '{$product->name}' no pertenece al proveedor seleccionado"
                ])->withInput();
            }
        }

        // TRANSACCIÓN PARA CONSISTENCIA
        try {
            DB::transaction(function () use ($validated) {
                // 1. Crear la compra
                $purchase = Purchase::create([
                    'supplier_id' => $validated['supplier_id'],
                    'date' => $validated['date'],
                ]);

                // 2. Crear los detalles
                foreach ($validated['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['unit_price'];

                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                    ]);
                }
            });

            return redirect()->route('purchases.index')->with('success', 'Compra registrada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al registrar la compra: ' . $e->getMessage()])->withInput();
        }
    }
}