<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($this->route('product'))],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'initial_stock' => ['nullable', 'integer', 'min:0'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio',
            'sale_price.required' => 'El precio de venta es obligatorio',
            'sale_price.min' => 'El precio de venta no puede ser negativo',
            'purchase_price.required' => 'El precio de compra es obligatorio',
            'purchase_price.min' => 'El precio de compra no puede ser negativo',
            'supplier_id.required' => 'El proveedor es obligatorio',
            'supplier_id.exists' => 'El proveedor seleccionado no existe',
        ];
    }
}