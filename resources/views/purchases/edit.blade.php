@extends('layouts.app')

@section('content')
<div class="mx-auto flex min-h-[calc(100vh-8rem)] items-start justify-center py-2">

    <!-- Modal Container -->
    <div class="w-full max-w-5xl">
        <div class="form-panel flex max-h-[calc(100vh-9rem)] flex-col">

            <!-- Sticky Header with Close Button -->
            <div class="flex shrink-0 items-center justify-between border-b border-line p-6 pb-4">
                <h2 class="text-xl font-bold tracking-tight text-white">Editar Compra</h2>
                <a href="{{ route('purchases.index') }}" class="icon-button" title="Cerrar">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>

            <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm" class="flex min-h-0 flex-1 flex-col">
                @csrf
                @method('PUT')

                <!-- Scrollable Body -->
                <div class="min-h-0 flex-1 overflow-y-auto">
                    <section class="form-section">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                            <!-- Supplier -->
                            <div>
                                <label for="supplier_id" class="field-label">Proveedor <span class="required">*</span></label>
                                <select name="supplier_id" id="supplier_id" required class="field-control appearance-none">
                                    <option value="">Seleccionar socio</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')<p class="field-error">{{ $message }}</p>@enderror
                            </div>

                            <!-- Date -->
                            <div>
                                <label for="purchase_date" class="field-label">Fecha de transacción <span class="required">*</span></label>
                                <input id="purchase_date" type="date" name="date" value="{{ old('date', $purchase->date) }}" required
                                       class="field-control">
                                @error('date')<p class="field-error">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </section>

                    <!-- Items Section -->
                    <section class="form-section">
                        <div class="mb-4 flex items-center justify-between gap-4">
                            <h3>Ítems de Adquisición</h3>
                            <button type="button" id="addItem" class="btn-secondary">
                                <i class="bi bi-plus-lg" aria-hidden="true"></i> Añadir Entrada
                            </button>
                        </div>

                        <div id="itemsContainer" class="space-y-3">
                            @foreach($purchase->details as $index => $detail)
                                <div class="item-row grid grid-cols-12 items-end gap-3 border border-line bg-surface-raised p-4 transition-colors hover:bg-surface-hover group">
                                    <div class="col-span-5">
                                        <label class="field-label">Selección de Producto</label>
                                        <select name="items[{{ $index }}][product_id]" class="product-select field-control appearance-none" required>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old('items.' . $index . '.product_id', $detail->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="field-label">Cant.</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ old('items.' . $index . '.quantity', $detail->quantity) }}" class="quantity-input field-control font-mono" required>
                                    </div>
                                    <div class="col-span-2">
                                        <label class="field-label">Costo Unit.</label>
                                        <input type="number" name="items[{{ $index }}][unit_price]" step="0.01" min="0" value="{{ old('items.' . $index . '.unit_price', $detail->unit_price) }}" class="unit-price-input field-control font-mono" required placeholder="0.00">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="field-label text-right">Subtotal</label>
                                        <div class="subtotal-display px-1 py-2 text-right font-mono text-sm font-semibold text-white">${{ number_format(old('items.' . $index . '.unit_price', $detail->unit_price) * old('items.' . $index . '.quantity', $detail->quantity), 2) }}</div>
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" class="remove-item btn-danger h-11 w-full px-0">
                                <i class="bi bi-trash text-xs" aria-hidden="true"></i><span class="sr-only">Eliminar ítem</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            @if($purchase->details->isEmpty() || !old('items'))
                                <div class="py-4 text-center text-sm italic text-neutral-400">No hay ítems. Agregue al menos uno.</div>
                            @endif
                        </div>

                        @error('items')
                            <p class="field-error mt-3">{{ $message }}</p>
                        @enderror
                    </section>
                </div>

                <!-- Sticky Footer -->
                <div class="form-actions">
                    <div class="flex items-baseline gap-3 sm:mr-auto">
                        <span class="text-xs font-semibold uppercase tracking-wider text-neutral-400">Valuación Total</span>
                        <span id="totalDisplay" class="font-mono text-3xl font-bold text-white">$0.00</span>
                    </div>

                    <div class="flex w-full items-center gap-3 sm:w-auto">
                        <a href="{{ route('purchases.index') }}" class="btn-secondary flex-1 sm:flex-none">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-primary flex-1 sm:flex-none">
                            Actualizar Compra
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hidden template for items -->
<template id="itemTemplate">
    <div class="item-row grid grid-cols-12 items-end gap-3 border border-line bg-surface-raised p-4 transition-colors hover:bg-surface-hover group">
        <div class="col-span-5">
            <label class="field-label">Selección de Producto</label>
            <select name="items[__INDEX__][product_id]" class="product-select field-control appearance-none" required>
                <option value="">Seleccionar ítem</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2">
            <label class="field-label">Cant.</label>
            <input type="number" name="items[__INDEX__][quantity]" min="1" value="1" class="quantity-input field-control font-mono" required>
        </div>
        <div class="col-span-2">
            <label class="field-label">Costo Unit.</label>
            <input type="number" name="items[__INDEX__][unit_price]" step="0.01" min="0" class="unit-price-input field-control font-mono" required placeholder="0.00">
        </div>
        <div class="col-span-2">
            <label class="field-label text-right">Subtotal</label>
            <div class="subtotal-display px-1 py-2 text-right font-mono text-sm font-semibold text-white">$0.00</div>
        </div>
        <div class="col-span-1">
            <button type="button" class="remove-item btn-danger h-11 w-full px-0">
                <i class="bi bi-trash text-xs"></i>
            </button>
        </div>
    </div>
</template>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemCount = {{ $purchase->details->count() }};
        const itemsContainer = document.getElementById('itemsContainer');
        const addItemBtn = document.getElementById('addItem');
        const totalDisplay = document.getElementById('totalDisplay');

        function addItem() {
            const template = document.getElementById('itemTemplate').innerHTML;
            const html = template.replace(/__INDEX__/g, itemCount);
            itemsContainer.insertAdjacentHTML('beforeend', html);
            itemCount++;
        }

        function updateTotal() {
            let total = 0;
            document.querySelectorAll('.item-row').forEach(row => {
                const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
                const subtotal = quantity * unitPrice;
                row.querySelector('.subtotal-display').textContent = '$' + subtotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                total += subtotal;
            });
            totalDisplay.textContent = '$' + total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

        addItemBtn.addEventListener('click', addItem);

        itemsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity-input') || e.target.classList.contains('unit-price-input')) {
                updateTotal();
            }
        });

        itemsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item')) {
                e.target.closest('.item-row').remove();
                updateTotal();
            }
        });

        // Initial total calculation
        updateTotal();
    });
</script>
@endpush
@endsection
