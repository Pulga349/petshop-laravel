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
        $validated = $request->validated();

        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->supplier_id != $validated['supplier_id']) {
                return back()->withErrors([
                    'items' => "El producto '{$product->name}' no pertenece al proveedor seleccionado"
                ])->withInput();
            }
        }

        try {
            DB::transaction(function () use ($validated) {
                // Calculate total from items
                $total = 0;
                foreach ($validated['items'] as $item) {
                    $total += $item['quantity'] * $item['unit_price'];
                }

                // 1. Crear la compra
                $purchase = Purchase::create([
                    'supplier_id' => $validated['supplier_id'],
                    'date' => $validated['date'],
                    'total' => $total,
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

    public function edit(Purchase $purchase): View
    {
        $purchase->load(['supplier', 'details.product']);
        $suppliers = Supplier::all();
        $products = Product::with('supplier')->get();
        return view('purchases.edit', compact('purchase', 'suppliers', 'products'));
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase): RedirectResponse
    {
        $validated = $request->validated();

        try {
            DB::transaction(function () use ($validated, $purchase) {
                // Delete existing details
                $purchase->details()->delete();

                // Calculate new total from items
                $total = 0;
                foreach ($validated['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['unit_price'];
                    $total += $subtotal;

                    PurchaseDetail::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal' => $subtotal,
                    ]);
                }

                // Update purchase with new total
                $purchase->update([
                    'supplier_id' => $validated['supplier_id'],
                    'date' => $validated['date'],
                    'total' => $total,
                ]);
            });

            return redirect()->route('purchases.index')->with('success', 'Compra actualizada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar la compra: ' . $e->getMessage()])->withInput();
        }
    }

    public function destroy(Purchase $purchase): RedirectResponse
    {
        try {
            DB::transaction(function () use ($purchase) {
                $purchase->details()->delete();
                $purchase->delete();
            });

            return redirect()->route('purchases.index')->with('success', 'Compra eliminada correctamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar la compra: ' . $e->getMessage()])->withInput();
        }
    }
}