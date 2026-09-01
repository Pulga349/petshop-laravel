<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'date' => ['required', 'date'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required' => 'El proveedor es obligatorio',
            'supplier_id.exists' => 'El proveedor seleccionado no existe',
            'date.required' => 'La fecha es obligatoria',
            'date.date' => 'La fecha debe ser una fecha válida',
            'items.required' => 'Debe agregar al menos un producto',
            'items.min' => 'Debe agregar al menos un producto',
            'items.*.product_id.required' => 'El producto es obligatorio',
            'items.*.product_id.exists' => 'El producto seleccionado no existe',
            'items.*.quantity.required' => 'La cantidad es obligatoria',
            'items.*.quantity.min' => 'La cantidad debe ser mayor a 0',
            'items.*.unit_price.required' => 'El precio unitario es obligatorio',
            'items.*.unit_price.min' => 'El precio no puede ser negativo',
        ];
    }
}