<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'exists:clients,id'],
            'date' => ['required', 'date'],
            // "sometimes": required whenever the client submits payment (the POS form always
            // does), while legacy callers that omit it keep working (existing test contract).
            'payment_method' => ['sometimes', 'required', 'string', Rule::in(array_keys(config('payment_methods')))],
            'amount_paid' => ['nullable', 'numeric', 'min:0', 'required_if:payment_method,efectivo'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'El cliente es obligatorio',
            'client_id.exists' => 'El cliente seleccionado no existe',
            'date.required' => 'La fecha es obligatoria',
            'date.date' => 'La fecha debe ser una fecha válida',
            'payment_method.required' => 'La forma de pago es obligatoria',
            'payment_method.string' => 'La forma de pago es inválida',
            'payment_method.in' => 'La forma de pago seleccionada no es válida',
            'amount_paid.numeric' => 'El monto recibido debe ser un número',
            'amount_paid.min' => 'El monto recibido no puede ser negativo',
            'amount_paid.required_if' => 'El monto recibido es obligatorio para pagos en efectivo',
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