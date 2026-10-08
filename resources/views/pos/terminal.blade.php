@extends('layouts.app')

@php
    $productPayload = $products->map(fn ($product) => [
        'id' => $product->id,
        'name' => $product->name,
        'sku' => $product->sku,
        'price' => (float) $product->sale_price,
        'stock' => (int) $product->stock,
        'image' => $product->image ? asset('storage/' . $product->image) : null,
        'category' => $product->category_name,
    ])->values()->all();

    $clientPayload = $clients->map(fn ($client) => [
        'id' => $client->id,
        'name' => $client->name,
        'tier' => $client->tier ?? 'Bronze',
    ])->values()->all();

    // Best-seller order restricted to products still present in the catalog.
    $productIdSet = array_flip(array_column($productPayload, 'id'));
    $bestIds = $bestSellers->pluck('product_id')
        ->filter(fn ($id) => isset($productIdSet[$id]))
        ->map(fn ($id) => (int) $id)
        ->values()
        ->all();

    // Precomputed scalars/arrays: @json() splits its expression on commas, so
    // anything with commas must be resolved to a single variable first.
    $posIvaRate = (float) $settings['iva_rate'];
    $posDiscounts = [
        'Bronze' => (float) $settings['discount_bronze'],
        'Silver' => (float) $settings['discount_silver'],
        'Gold' => (float) $settings['discount_gold'],
        'Platinum' => (float) $settings['discount_platinum'],
    ];
@endphp

@section('content')
<style>[x-cloak] { display: none !important; }</style>

<div x-data="posTerminal()" @keydown.window="handleGlobalKey($event)" class="flex flex-col">
    <div class="grid grid-cols-1 gap-4 lg:h-[calc(100vh-7.5rem)] lg:min-h-[36rem] lg:grid-cols-[minmax(0,1fr)_400px] lg:grid-rows-1 xl:grid-cols-[minmax(0,1fr)_440px]">

        <!-- Zone 1 — Catálogo rápido -->
        <section class="flex min-h-0 flex-col border border-line bg-surface">
            <div class="shrink-0 space-y-3 border-b border-line p-4">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-base font-semibold text-white">Catálogo rápido</h2>
                    <span class="status-badge border border-line bg-surface-raised text-neutral-300">
                        <span class="mr-1.5 h-1.5 w-1.5 bg-accent" aria-hidden="true"></span>
                        <span class="font-mono" x-text="products.length"></span>&nbsp;productos
                    </span>
                </div>

                <!-- Client-side filter / scanner-as-keyboard input (outside the form: Enter never implicit-submits) -->
                <div class="relative">
                    <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500" aria-hidden="true"></i>
                    <input type="search" x-model="search" autofocus
                           aria-label="Buscar producto"
                           placeholder="Buscar por SKU, nombre o escanear código…"
                           class="field-control pl-10">
                </div>

                <!-- Category tabs -->
                <div class="flex gap-2 overflow-x-auto pb-1">
                    <button type="button"
                            @click="tab = '__best__'"
                            :class="tab === '__best__' ? 'border border-line bg-surface-hover text-white' : 'border border-line bg-surface text-neutral-400 hover:bg-surface-hover hover:text-white'"
                            class="shrink-0 px-3 py-2 text-sm font-medium transition-colors">Más Vendidos</button>
                    @foreach($categories as $category)
                        <button type="button"
                                @click="tab = '{{ $category->name }}'"
                                :class="tab === '{{ $category->name }}' ? 'border border-line bg-surface-hover text-white' : 'border border-line bg-surface text-neutral-400 hover:bg-surface-hover hover:text-white'"
                                class="shrink-0 px-3 py-2 text-sm font-medium transition-colors">{{ $category->name }}</button>
                    @endforeach
                </div>
            </div>

            <!-- Product grid -->
            <div class="min-h-0 flex-1 overflow-y-auto p-4">
                <div class="grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-4">
                    <template x-for="p in visibleProducts" :key="p.id">
                        <button type="button"
                                class="flex w-full flex-col border border-line bg-surface text-left transition-colors enabled:hover:bg-surface-hover disabled:cursor-not-allowed disabled:opacity-40"
                                :disabled="remaining(p) <= 0"
                                @click="addToCart(p)"
                                :aria-label="'Añadir al ticket: ' + p.name">
                            <div class="flex aspect-square w-full items-center justify-center overflow-hidden border-b border-line bg-surface-raised">
                                <template x-if="p.image">
                                    <img :src="p.image" alt="" class="h-full w-full object-cover">
                                </template>
                                <template x-if="!p.image">
                                    <i class="bi bi-image text-2xl text-neutral-500" aria-hidden="true"></i>
                                </template>
                            </div>
                            <div class="flex flex-col gap-1 p-3">
                                <p class="truncate text-sm font-semibold text-white" :title="p.name" x-text="p.name"></p>
                                <p class="font-mono text-xs text-neutral-400" x-text="'SKU ' + (p.sku || '—')"></p>
                                <span class="mt-1 font-mono text-lg font-semibold text-white" x-text="money(p.price)"></span>
                                <div class="mt-1 flex justify-end">
                                    <span x-show="p.stock <= 0" x-cloak
                                          class="status-badge border border-danger/30 bg-danger/10 text-white">
                                        <span class="mr-1.5 h-1.5 w-1.5 bg-danger" aria-hidden="true"></span>Agotado
                                    </span>
                                    <span x-show="p.stock > 0 && remaining(p) === 1" x-cloak
                                          class="status-badge border border-warning/30 bg-warning/10 text-white">
                                        <span class="mr-1.5 h-1.5 w-1.5 bg-warning" aria-hidden="true"></span>ÚLTIMA UNIDAD
                                    </span>
                                    <span x-show="p.stock > 0 && remaining(p) !== 1"
                                          class="status-badge border border-line bg-surface-raised text-white"
                                          x-text="remaining(p) + ' u'"></span>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="visibleProducts.length === 0" x-cloak class="py-16 text-center">
                    <i class="bi bi-search mb-3 block text-3xl text-neutral-500" aria-hidden="true"></i>
                    <p class="font-semibold text-white">Sin resultados</p>
                    <p class="mt-1 text-sm text-neutral-400">Probá con otro SKU o nombre.</p>
                </div>
            </div>
        </section>

        <!-- Zones 2 + 3 — Ticket de venta y pago -->
        <form x-ref="form" method="POST" action="{{ route('sales.store') }}"
              class="flex min-h-0 flex-col border border-line bg-surface lg:h-full lg:overflow-hidden">
            @csrf

            <!-- Real POST fields for the server contract -->
            <input type="hidden" name="date" value="{{ old('date', date('Y-m-d')) }}">
            <input type="hidden" name="payment_method" :value="paymentMethod">
            <template x-for="(item, index) in cart" :key="item.product_id">
                <div>
                    <input type="hidden" :name="'items[' + index + '][product_id]'" :value="item.product_id">
                    <input type="hidden" :name="'items[' + index + '][quantity]'" :value="item.quantity">
                    <input type="hidden" :name="'items[' + index + '][unit_price]'" :value="item.unit_price">
                </div>
            </template>

            <!-- Scrollable region: header + client + item rows (single scroller, no nesting) -->
            <div class="min-h-0 flex-1 overflow-y-auto overscroll-contain bg-surface">
                @if($errors->any())
                    <div class="border-b border-danger/30 bg-danger/10 p-4" role="alert">
                        <div class="flex gap-2">
                            <i class="bi bi-exclamation-triangle-fill mt-0.5 text-white" aria-hidden="true"></i>
                            <div>
                                <p class="text-sm font-semibold text-white">No se pudo registrar la venta</p>
                                <ul class="mt-1 space-y-0.5 text-xs text-white">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ticket header -->
                <div class="flex items-center justify-between gap-3 border-b border-line bg-surface p-4">
                    <h2 class="text-base font-semibold text-white">Ticket de Venta</h2>
                    <span class="status-badge border border-line bg-surface-raised text-white">
                        <span class="font-mono" x-text="cartQty"></span>&nbsp;ítems
                    </span>
                </div>

                <!-- Cliente -->
                <div class="border-b border-line bg-surface p-4">
                    <label for="client_id" class="field-label">Cliente <span class="required">*</span></label>
                    <select name="client_id" id="client_id" x-model="clientId" required class="field-control">
                        <option value="">Seleccioná un cliente</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                    <div class="mt-2" x-show="selectedClient" x-cloak>
                        <span class="status-badge border border-line bg-surface-raised text-white">
                            <span class="mr-1.5 h-1.5 w-1.5 bg-accent" aria-hidden="true"></span>
                            <span x-text="tierBadge"></span>
                        </span>
                    </div>
                </div>

                <!-- Item rows -->
                <div>
                    <div x-show="cart.length === 0" x-cloak class="px-4 py-10 text-center">
                        <i class="bi bi-cart mb-2 block text-2xl text-neutral-500" aria-hidden="true"></i>
                        <p class="text-sm text-neutral-400">Sin ítems — seleccioná productos del catálogo</p>
                    </div>
                    <template x-for="(item, index) in cart" :key="item.product_id">
                        <div class="flex items-center gap-2 border-b border-line bg-surface px-4 py-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-white" :title="item.name" x-text="item.name"></p>
                                <p class="font-mono text-xs text-neutral-400" x-text="money(item.unit_price) + ' c/u'"></p>
                            </div>
                            <div class="flex shrink-0 items-center border border-line bg-surface-raised">
                                <button type="button"
                                        class="flex h-8 w-8 items-center justify-center font-mono text-neutral-400 transition-colors hover:bg-surface-hover hover:text-white disabled:opacity-40"
                                        @click="decQty(item)" :disabled="item.quantity <= 1"
                                        aria-label="Restar cantidad">−</button>
                                <span class="w-8 text-center font-mono text-sm text-white" x-text="item.quantity"></span>
                                <button type="button"
                                        class="flex h-8 w-8 items-center justify-center font-mono text-neutral-400 transition-colors hover:bg-surface-hover hover:text-white disabled:opacity-40"
                                        @click="incQty(item)" :disabled="item.quantity >= stockOf(item.product_id)"
                                        aria-label="Sumar cantidad">+</button>
                            </div>
                            <span class="w-20 shrink-0 text-right font-mono text-sm font-semibold text-white"
                                  x-text="money(item.quantity * item.unit_price)"></span>
                            <button type="button"
                                    class="flex h-8 w-8 shrink-0 items-center justify-center border border-danger/30 bg-danger/10 text-white transition-colors hover:border-accent"
                                    @click="removeItem(index)" aria-label="Eliminar ítem">
                                <i class="bi bi-trash text-xs" aria-hidden="true"></i>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Fixed footer: totals + payment + actions (never covered by item rows) -->
            <div class="shrink-0 border-t border-line bg-surface">
                <!-- Totals -->
                <div class="border-b border-line p-4">
                    <dl class="space-y-2 text-sm">
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">Neto</dt>
                            <dd class="font-mono text-white" x-text="money(neto)"></dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">Descuento</dt>
                            <dd class="font-mono text-white"
                                x-text="discountAmount > 0 ? '−' + money(discountAmount) + ' · ' + discountPct + '%' : '—'"></dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-neutral-400">IVA {{ 0 + $settings['iva_rate'] }}% Discriminado</dt>
                            <dd class="font-mono text-white" x-text="money(ivaAmount)"></dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 border-t border-line pt-3">
                            <dt class="text-base font-semibold text-white">Total a pagar</dt>
                            <dd class="font-mono text-2xl font-bold text-white" x-text="money(total)"></dd>
                        </div>
                    </dl>
                </div>

                <!-- Zone 3 — Pago -->
                <div class="border-b border-line p-4">
                <p class="field-label">Método de pago <span class="required">*</span></p>
                @php($paymentIcons = ['efectivo' => 'bi-cash', 'tarjeta' => 'bi-credit-card', 'qr' => 'bi-qr-code', 'cuenta_corriente' => 'bi-journal-text'])
                <div class="grid grid-cols-2 gap-2">
                    @foreach(config('payment_methods') as $paymentCode => $paymentLabel)
                        <button type="button" @click="paymentMethod = '{{ $paymentCode }}'"
                                :class="paymentMethod === '{{ $paymentCode }}' ? 'border border-white bg-white text-black' : 'border border-line bg-surface-raised text-white hover:bg-surface-hover'"
                                class="flex min-h-11 items-center justify-center gap-2 px-3 text-sm font-semibold transition-colors">
                            <i class="bi {{ $paymentIcons[$paymentCode] ?? 'bi-credit-card' }}" aria-hidden="true"></i> {{ $paymentLabel }}
                        </button>
                    @endforeach
                </div>

                <!-- Cash only: amount_paid is omitted from the POST for other methods (x-if removes the node) -->
                <template x-if="paymentMethod === 'efectivo'">
                    <div class="mt-3">
                        <label for="amount_paid" class="field-label">Paga con…</label>
                        <input id="amount_paid" name="amount_paid" type="number" min="0" step="0.01"
                               inputmode="decimal" x-model="amountPaid"
                               class="field-control font-mono" placeholder="0.00">
                    </div>
                </template>

                <div x-show="paymentMethod === 'efectivo' && amountPaid !== '' && amountPaid !== null" x-cloak class="mt-2">
                    <template x-if="cashShort <= 0">
                        <div class="flex items-baseline justify-between gap-4">
                            <span class="text-sm text-neutral-400">Vuelto</span>
                            <span class="font-mono text-base font-semibold text-white" x-text="money(cashChange)"></span>
                        </div>
                    </template>
                    <template x-if="cashShort > 0">
                        <div class="flex items-baseline justify-between gap-4">
                            <span class="status-badge border border-danger/30 bg-danger/10 text-white">
                                <span class="mr-1.5 h-1.5 w-1.5 bg-danger" aria-hidden="true"></span>Pago insuficiente
                            </span>
                            <span class="status-badge border border-danger/30 bg-danger/10 font-mono text-white" x-text="'Faltan ' + money(cashShort)"></span>
                        </div>
                    </template>
                </div>
            </div>
            </div>

            <!-- Actions -->
            <div class="shrink-0 space-y-3 border-t border-line bg-surface-raised p-4">
                <button type="submit" class="btn-primary w-full disabled:cursor-not-allowed disabled:opacity-40"
                        :disabled="!canSubmit">
                    <i class="bi bi-check-lg" aria-hidden="true"></i> Confirmar y Facturar [Enter]
                </button>
                <a href="{{ route('sales.index') }}" class="btn-secondary w-full">Cancelar</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    window.posTerminal = () => ({
        search: '',
        tab: '__best__',
        cart: [],
        clientId: @json(old('client_id', '')),
        paymentMethod: @json(old('payment_method', '')),
        amountPaid: @json(old('amount_paid', '')),
        cancelUrl: @json(route('sales.index')),
        ivaRate: @json($posIvaRate),
        discounts: @json($posDiscounts),
        products: @json($productPayload),
        clients: @json($clientPayload),
        bestIds: @json($bestIds),

        // ---- Computed (mirror SaleController@store math for live display only) ----
        get selectedClient() {
            if (this.clientId === '' || this.clientId === null || this.clientId === undefined) return null;
            return this.clients.find(c => String(c.id) === String(this.clientId)) || null;
        },
        get tierBadge() {
            const client = this.selectedClient;
            if (!client) return '';
            return client.tier + ' · ' + (this.discounts[client.tier] ?? 0) + '%';
        },
        get discountPct() {
            const client = this.selectedClient;
            return client ? (this.discounts[client.tier] ?? 0) : 0;
        },
        get neto() {
            return this.r2(this.cart.reduce((sum, item) => sum + item.quantity * item.unit_price, 0));
        },
        get discountAmount() {
            return this.r2(this.neto * this.discountPct / 100);
        },
        get total() {
            return this.r2(this.neto - this.discountAmount);
        },
        get ivaAmount() {
            if (this.ivaRate <= 0) return 0;
            return this.r2(this.total * this.ivaRate / (100 + this.ivaRate));
        },
        get cartQty() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },
        get pago() {
            const value = parseFloat(this.amountPaid);
            return isNaN(value) ? 0 : value;
        },
        get cashChange() {
            return Math.max(0, this.pago - this.total);
        },
        get cashShort() {
            if (this.paymentMethod !== 'efectivo') return 0;
            if (this.amountPaid === '' || this.amountPaid === null || this.amountPaid === undefined) return 0;
            return Math.max(0, this.total - this.pago);
        },
        get canSubmit() {
            if (!this.clientId) return false;
            if (this.cart.length === 0) return false;
            if (!this.paymentMethod) return false;
            if (this.paymentMethod === 'efectivo') {
                const value = parseFloat(this.amountPaid);
                if (isNaN(value) || value + 1e-9 < this.total) return false;
            }
            return true;
        },
        get visibleProducts() {
            let list = this.products;
            const query = this.search.trim().toLowerCase();
            if (query) {
                list = list.filter(p =>
                    (p.name || '').toLowerCase().includes(query) ||
                    (p.sku || '').toLowerCase().includes(query)
                );
            }
            if (this.tab === '__best__') {
                // Best sellers first (controller order), then the rest; empty bestIds keeps catalog order.
                const rank = new Map(this.bestIds.map((id, i) => [id, i]));
                return [...list].sort((a, b) => (rank.get(a.id) ?? 1e9) - (rank.get(b.id) ?? 1e9));
            }
            return list.filter(p => p.category === this.tab);
        },

        // ---- Helpers / actions ----
        r2(n) {
            return Math.round((Number(n) + Number.EPSILON) * 100) / 100;
        },
        money(n) {
            const value = this.r2(n);
            return '$' + value.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },
        inCart(productId) {
            const line = this.cart.find(i => i.product_id === productId);
            return line ? line.quantity : 0;
        },
        stockOf(productId) {
            const product = this.products.find(p => p.id === productId);
            return product ? product.stock : 0;
        },
        remaining(product) {
            return product.stock - this.inCart(product.id);
        },
        addToCart(product) {
            if (product.stock <= 0) return;
            if (this.inCart(product.id) >= product.stock) return;
            const line = this.cart.find(i => i.product_id === product.id);
            if (line) {
                line.quantity++;
            } else {
                this.cart.push({ product_id: product.id, name: product.name, unit_price: product.price, quantity: 1 });
            }
        },
        incQty(item) {
            if (item.quantity < this.stockOf(item.product_id)) item.quantity++;
        },
        decQty(item) {
            if (item.quantity > 1) item.quantity--;
        },
        removeItem(index) {
            this.cart.splice(index, 1);
        },
        submitSale() {
            const form = this.$refs.form;
            if (!form) return;
            if (typeof form.requestSubmit === 'function') form.requestSubmit();
            else form.submit();
        },
        handleGlobalKey(event) {
            // Escape aborts the ticket and returns to the sales list (same as Cancelar).
            if (event.key === 'Escape') {
                event.preventDefault();
                window.location.href = this.cancelUrl;
                return;
            }
            if (event.key !== 'Enter') return;

            const target = event.target;
            const tag = (target.tagName || '').toLowerCase();
            const type = (target.type || '').toLowerCase();

            // Let buttons/links and text-like inputs keep their native Enter behavior.
            if (tag === 'button' || tag === 'a' || tag === 'textarea' || tag === 'select') return;
            if (tag === 'input' && type !== 'number') return;

            if (!this.canSubmit) {
                // Block implicit form submission from the cash amount field until valid.
                if (tag === 'input') event.preventDefault();
                return;
            }
            event.preventDefault();
            this.submitSale();
        },
    });
</script>
@endpush
@endsection
