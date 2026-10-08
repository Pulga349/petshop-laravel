@extends('layouts.app')

@section('header')
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-accent">Ajustes</p>
            <h1>Configuración</h1>
            <p>Parámetros fiscales y descuentos aplicados al registrar ventas.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn-secondary">Volver</a>
    </div>
@endsection

@section('content')
<div class="mx-auto max-w-3xl">
    <form action="{{ route('settings.update') }}" method="POST" class="form-panel">
        @csrf
        @method('PUT')

        <section class="form-section">
            <h3>Impuestos</h3>
            <p>Los precios ya incluyen IVA; este valor define la porción discriminada en el comprobante.</p>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="iva_rate" class="field-label">IVA (%) <span class="required">*</span></label>
                    <input id="iva_rate" name="iva_rate" type="number" step="0.01" min="0" max="100"
                           value="{{ old('iva_rate', $settings['iva_rate']) }}" required class="field-control font-mono">
                    @error('iva_rate')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="form-section">
            <h3>Descuentos por nivel de cliente</h3>
            <p>Descuento automático aplicado según el nivel del cliente al momento de la venta.</p>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="discount_bronze" class="field-label">Bronze (%) <span class="required">*</span></label>
                    <input id="discount_bronze" name="discount_bronze" type="number" step="0.01" min="0" max="100"
                           value="{{ old('discount_bronze', $settings['discount_bronze']) }}" required class="field-control font-mono">
                    @error('discount_bronze')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="discount_silver" class="field-label">Silver (%) <span class="required">*</span></label>
                    <input id="discount_silver" name="discount_silver" type="number" step="0.01" min="0" max="100"
                           value="{{ old('discount_silver', $settings['discount_silver']) }}" required class="field-control font-mono">
                    @error('discount_silver')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="discount_gold" class="field-label">Gold (%) <span class="required">*</span></label>
                    <input id="discount_gold" name="discount_gold" type="number" step="0.01" min="0" max="100"
                           value="{{ old('discount_gold', $settings['discount_gold']) }}" required class="field-control font-mono">
                    @error('discount_gold')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="discount_platinum" class="field-label">Platinum (%) <span class="required">*</span></label>
                    <input id="discount_platinum" name="discount_platinum" type="number" step="0.01" min="0" max="100"
                           value="{{ old('discount_platinum', $settings['discount_platinum']) }}" required class="field-control font-mono">
                    @error('discount_platinum')<p class="field-error">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <div class="form-actions">
            <a href="{{ route('dashboard') }}" class="btn-secondary">Cancelar</a>
            <button type="submit" class="btn-primary">Guardar</button>
        </div>
    </form>
</div>
@endsection
