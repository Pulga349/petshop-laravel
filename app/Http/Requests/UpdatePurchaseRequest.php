<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'date' => ['nullable', 'date'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.exists' => 'El proveedor seleccionado no existe',
            'date.date' => 'La fecha debe ser una fecha válida',
            'items.min' => 'Debe agregar al menos un producto',
            'items.*.product_id.exists' => 'El producto seleccionado no existe',
            'items.*.quantity.min' => 'La cantidad debe ser mayor a 0',
            'items.*.unit_price.min' => 'El precio no puede ser negativo',
        ];
    }
}