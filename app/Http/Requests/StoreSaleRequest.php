<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('caisse.manage') || $this->user()->can('pharmacie.sell');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['nullable', 'exists:patients,id'],
            'module' => ['required', Rule::in(['caisse', 'pharmacie', 'consultation', 'examen', 'hospitalisation', 'intervention', 'accouchement', 'bloc'])],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.method' => ['required', Rule::in(['cash', 'mobile_money', 'transfer'])],
            'payments.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payments.*.reference' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Ajoutez au moins une ligne à la vente.',
            'payments.required' => 'Indiquez au moins un mode de paiement.',
        ];
    }
}
