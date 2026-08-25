<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('stock.inventory');
    }

    public function rules(): array
    {
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
            'counts' => ['required', 'array', 'min:1'],
            'counts.*.product_id' => ['required', 'exists:products,id'],
            'counts.*.counted_quantity' => ['required', 'integer', 'min:0'],
        ];
    }
}
