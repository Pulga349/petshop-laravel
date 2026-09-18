@extends('layouts.app')

@section('content')
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Blurred Background Overlay -->
    <div class="absolute inset-0 bg-[#0b0c10]/60 backdrop-blur-xl"
         onclick="window.location.href='{{ route('purchases.index') }}'"></div>

    <!-- Modal Container -->
    <div class="relative w-full max-w-5xl transform transition-all">
        <!-- Subtle outer glow -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-[2rem] blur-2xl opacity-50"></div>

        <!-- Glassmorphism Modal -->
        <div class="relative bg-[#1a1c23]/40 backdrop-blur-3xl border border-white/10 rounded-[2rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)] flex flex-col max-h-[85vh]">

            <!-- Sticky Header with Close Button -->
            <div class="flex items-center justify-between p-8 pb-4 shrink-0">
                <div class="flex-1"></div>
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-black text-white tracking-tight uppercase">Editar Compra</h2>
                    <div class="h-1 w-16 bg-blue-600 mx-auto mt-2 rounded-full"></div>
                </div>
                <div class="flex-1 flex justify-end">
                    <a href="{{ route('purchases.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all" title="Cerrar">
                        <i class="bi bi-x-lg text-lg"></i>
                    </a>
                </div>
            </div>

            <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')

                <!-- Scrollable Body -->
                <div class="flex-1 overflow-y-auto px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <!-- Supplier -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Proveedor Global</label>
                            <select name="supplier_id" id="supplier_id" required
                                    class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 appearance-none transition-all text-left">
                                <option value="">Seleccionar socio</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchase->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Fecha de Transacción</label>
                            <input type="date" name="date" value="{{ old('date', $purchase->date) }}" required
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all">
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="bg-white/5 border border-white/5 rounded-2xl p-6 mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-[10px] font-black text-white uppercase tracking-[0.2em]">Ítems de Adquisición</h3>
                            <button type="button" id="addItem" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-[9px] font-black rounded-lg transition-all shadow-lg shadow-blue-600/20 uppercase tracking-widest">
                                <i class="bi bi-plus-lg mr-1"></i> Añadir Entrada
                            </button>
                        </div>

                        <div id="itemsContainer" class="space-y-3">
                            @foreach($purchase->details as $index => $detail)
                                <div class="item-row grid grid-cols-12 gap-3 items-end p-4 bg-white/5 border border-white/5 rounded-xl group transition-colors hover:border-white/10">
                                    <div class="col-span-5 space-y-1.5">
                                        <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1 text-left block">Selección de Producto</label>
                                        <select name="items[{{ $index }}][product_id]" class="product-select w-full px-3 py-2 bg-[#1a1c23] border border-white/5 rounded-lg text-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all text-left" required>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ old('items.' . $index . '.product_id', $detail->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-2 space-y-1.5 text-left">
                                        <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1">Cant.</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ old('items.' . $index . '.quantity', $detail->quantity) }}" class="quantity-input w-full px-3 py-2 bg-[#1a1c23] border border-white/5 rounded-lg text-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" required>
                                    </div>
                                    <div class="col-span-2 space-y-1.5 text-left">
                                        <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1">Costo Unit.</label>
                                        <input type="number" name="items[{{ $index }}][unit_price]" step="0.01" min="0" value="{{ old('items.' . $index . '.unit_price', $detail->unit_price) }}" class="unit-price-input w-full px-3 py-2 bg-[#1a1c23] border border-white/5 rounded-lg text-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" required placeholder="0.00">
                                    </div>
                                    <div class="col-span-2 space-y-1.5">
                                        <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1 text-right block pr-2">Subtotal</label>
                                        <div class="subtotal-display px-3 py-2 text-white font-black text-xs text-right">${{ number_format(old('items.' . $index . '.unit_price', $detail->unit_price) * old('items.' . $index . '.quantity', $detail->quantity), 2) }}</div>
                                    </div>
                                    <div class="col-span-1">
                                        <button type="button" class="remove-item w-full h-8 flex items-center justify-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-lg transition-all">
                                            <i class="bi bi-trash text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                            @if($purchase->details->isEmpty() || !old('items'))
                                <div class="text-center text-gray-500 text-sm italic py-4">No hay ítems. Agregue al menos uno.</div>
                            @endif
                        </div>

                        @error('items')
                            <p class="mt-3 text-[10px] font-bold text-red-500 uppercase tracking-widest">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Sticky Footer -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-6 px-8 pb-8 pt-6 border-t border-white/5 shrink-0">
                    <div class="flex items-baseline gap-3">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Valuación Total</span>
                        <span id="totalDisplay" class="text-2xl font-black text-emerald-400 tracking-tight">$0.00</span>
                    </div>

                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <a href="{{ route('purchases.index') }}"
                           class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white text-xs font-black rounded-xl transition-all border border-white/5 uppercase tracking-widest text-center flex-1 sm:flex-none">
                            Cancelar
                        </a>
                        <button type="submit"
                                class="flex-1 sm:flex-none px-8 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] uppercase tracking-[0.2em]">
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
    <div class="item-row grid grid-cols-12 gap-3 items-end p-4 bg-white/5 border border-white/5 rounded-xl group transition-colors hover:border-white/10">
        <div class="col-span-5 space-y-1.5">
            <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1 text-left block">Selección de Producto</label>
            <select name="items[__INDEX__][product_id]" class="product-select w-full px-3 py-2 bg-[#1a1c23] border border-white/5 rounded-lg text-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all text-left" required>
                <option value="">Seleccionar ítem</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-span-2 space-y-1.5 text-left">
            <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1">Cant.</label>
            <input type="number" name="items[__INDEX__][quantity]" min="1" value="1" class="quantity-input w-full px-3 py-2 bg-[#1a1c23] border border-white/5 rounded-lg text-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" required>
        </div>
        <div class="col-span-2 space-y-1.5 text-left">
            <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1">Costo Unit.</label>
            <input type="number" name="items[__INDEX__][unit_price]" step="0.01" min="0" class="unit-price-input w-full px-3 py-2 bg-[#1a1c23] border border-white/5 rounded-lg text-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all" required placeholder="0.00">
        </div>
        <div class="col-span-2 space-y-1.5">
            <label class="text-[8px] font-black text-gray-600 uppercase tracking-[0.2em] ml-1 text-right block pr-2">Subtotal</label>
            <div class="subtotal-display px-3 py-2 text-white font-black text-xs text-right">$0.00</div>
        </div>
        <div class="col-span-1">
            <button type="button" class="remove-item w-full h-8 flex items-center justify-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-lg transition-all">
                <i class="bi bi-trash text-xs"></i>
            </button>
        </div>
    </div>
</template>

<!-- Background Content -->
<div class="opacity-10 pointer-events-none blur-md">
    <div class="p-8 space-y-8">
        <div class="h-10 w-48 bg-gray-700 rounded-full"></div>
        <div class="bg-gray-800 rounded-[2.5rem] h-[600px]"></div>
    </div>
</div>

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
            updateTotal();
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
