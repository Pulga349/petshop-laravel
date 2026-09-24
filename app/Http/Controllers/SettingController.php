<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Show the runtime settings window (VAT rate + tier discounts).
     */
    public function show(): View
    {
        $settings = [
            'iva_rate' => Setting::get('iva_rate', 21),
            'discount_bronze' => Setting::get('discount_bronze', 0),
            'discount_silver' => Setting::get('discount_silver', 5),
            'discount_gold' => Setting::get('discount_gold', 10),
            'discount_platinum' => Setting::get('discount_platinum', 15),
        ];

        return view('settings.edit', compact('settings'));
    }

    /**
     * Persist validated settings and flash the standard success toast.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'iva_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_bronze' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_silver' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_gold' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_platinum' => ['required', 'numeric', 'min:0', 'max:100'],
        ], [
            'iva_rate.required' => 'El valor de IVA es obligatorio',
            'iva_rate.numeric' => 'El valor de IVA debe ser numérico',
            'iva_rate.min' => 'El valor de IVA no puede ser negativo',
            'iva_rate.max' => 'El valor de IVA no puede superar 100',
            'discount_bronze.required' => 'El descuento de Bronze es obligatorio',
            'discount_bronze.numeric' => 'El descuento debe ser numérico',
            'discount_bronze.min' => 'El descuento no puede ser negativo',
            'discount_bronze.max' => 'El descuento no puede superar 100',
            'discount_silver.required' => 'El descuento de Silver es obligatorio',
            'discount_silver.numeric' => 'El descuento debe ser numérico',
            'discount_silver.min' => 'El descuento no puede ser negativo',
            'discount_silver.max' => 'El descuento no puede superar 100',
            'discount_gold.required' => 'El descuento de Gold es obligatorio',
            'discount_gold.numeric' => 'El descuento debe ser numérico',
            'discount_gold.min' => 'El descuento no puede ser negativo',
            'discount_gold.max' => 'El descuento no puede superar 100',
            'discount_platinum.required' => 'El descuento de Platinum es obligatorio',
            'discount_platinum.numeric' => 'El descuento debe ser numérico',
            'discount_platinum.min' => 'El descuento no puede ser negativo',
            'discount_platinum.max' => 'El descuento no puede superar 100',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, (string) $value);
        }

        return back()->with('success', 'Configuración actualizada correctamente');
    }
}
