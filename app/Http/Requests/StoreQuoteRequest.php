<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'client_id'   => ['required', 'exists:clients,id'],
            'title'       => ['required', 'string', 'max:255'],
            'currency'    => ['required', 'in:CLP,USD'],
            'tax_rate'    => ['required', 'integer', 'min:0', 'max:100'],
            'tax_included'=> ['boolean'],
            'valid_until' => ['nullable', 'date', 'after:today'],
            'notes'       => ['nullable', 'string'],
            'terms'       => ['nullable', 'string'],

            // Items (mínimo 1)
            'items'               => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.detail'      => ['nullable', 'string'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],
            'items.*.unit_price'  => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required'           => 'Selecciona un cliente.',
            'title.required'               => 'El título es obligatorio.',
            'items.required'               => 'Agrega al menos un ítem.',
            'items.min'                    => 'La cotización debe tener al menos 1 ítem.',
            'items.*.description.required' => 'Cada ítem debe tener una descripción.',
            'items.*.quantity.required'    => 'Ingresa la cantidad.',
            'items.*.unit_price.required'  => 'Ingresa el precio unitario.',
            'valid_until.after'            => 'La fecha de vigencia debe ser futura.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Limpia precios si vienen con formato (ej: "1.500.000")
        if ($this->has('items')) {
            $items = collect($this->items)->map(function ($item) {
                $item['unit_price'] = (int) preg_replace('/[^0-9]/', '', $item['unit_price'] ?? 0);
                return $item;
            })->all();
            $this->merge(['items' => $items]);
        }

        $this->merge(['tax_included' => $this->boolean('tax_included')]);
    }
}
