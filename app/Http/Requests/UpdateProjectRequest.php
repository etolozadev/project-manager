<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id'          => ['required', 'exists:clients,id'],
            'name'               => ['required', 'string', 'max:255'],
            'description'        => ['nullable', 'string'],
            'status'             => ['required', 'in:draft,active,paused,completed,cancelled'],
            'start_date'         => ['nullable', 'date'],
            'end_date'           => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget_amount'      => ['required', 'integer', 'min:0'],
            'currency'           => ['required', 'in:CLP,USD'],
            'budget_includes_vat'=> ['boolean'],
            'notes'              => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required'     => 'Selecciona un cliente.',
            'name.required'          => 'El nombre del proyecto es obligatorio.',
            'status.required'        => 'Selecciona un estado.',
            'budget_amount.required' => 'Ingresa el presupuesto.',
            'end_date.after_or_equal'=> 'La fecha de término debe ser posterior al inicio.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->budget_amount) {
            $this->merge([
                'budget_amount' => (int) preg_replace('/[^0-9]/', '', $this->budget_amount),
            ]);
        }

        $this->merge([
            'budget_includes_vat' => $this->boolean('budget_includes_vat'),
        ]);
    }
}
