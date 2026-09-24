<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'exists:clients,id'],
            'date' => ['nullable', 'date'],
            // Optional: the classic edit form may not send payment fields for old sales.
            'payment_method' => ['nullable', 'string', Rule::in(array_keys(config('payment_methods')))],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.product_id' => ['required_with:items', 'exists:products,id'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'El cliente seleccionado no existe',
            'date.date' => 'La fecha debe ser una fecha válida',
            'payment_method.string' => 'La forma de pago es inválida',
            'payment_method.in' => 'La forma de pago seleccionada no es válida',
            'amount_paid.numeric' => 'El monto recibido debe ser un número',
            'amount_paid.min' => 'El monto recibido no puede ser negativo',
            'items.min' => 'Debe agregar al menos un producto',
            'items.*.product_id.exists' => 'El producto seleccionado no existe',
            'items.*.quantity.min' => 'La cantidad debe ser mayor a 0',
            'items.*.unit_price.min' => 'El precio no puede ser negativo',
        ];
    }
}