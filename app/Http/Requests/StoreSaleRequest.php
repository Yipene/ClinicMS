<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSaleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_type' => $this->input(
                'customer_type',
                $this->filled('patient_id') ? 'patient' : 'anonymous'
            ),
        ]);
    }

protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
{
    throw new \Illuminate\Validation\ValidationException($validator, redirect()->back()
        ->withInput()
        ->withErrors($validator, 'sale'));
}

    public function authorize(): bool
    {
        return $this->user()->can('pharmacie.sell');
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['nullable', 'required_if:customer_type,patient', 'exists:patients,id'],
            'customer_type' => ['required', Rule::in(['patient', 'external', 'anonymous'])],
            'customer_name' => ['nullable', 'required_if:customer_type,external', 'string', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'module' => ['required', Rule::in(['pharmacie'])],
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
