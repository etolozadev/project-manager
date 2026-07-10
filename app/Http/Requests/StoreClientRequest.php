<?php

namespace App\Http\Requests;

use App\Rules\ValidRut;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'           => ['required', 'in:person,company'],
            'name'           => ['required', 'string', 'max:255'],
            'rut'            => ['required', 'string', new ValidRut, 'unique:clients,rut_raw,' . null],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'address'        => ['nullable', 'string', 'max:255'],
            'city'           => ['nullable', 'string', 'max:100'],
            'region'         => ['nullable', 'string', 'max:100'],
            'website'        => ['nullable', 'url', 'max:255'],
            'notes'          => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'  => 'Selecciona el tipo de cliente.',
            'name.required'  => 'El nombre es obligatorio.',
            'rut.required'   => 'El RUT es obligatorio.',
            'rut.unique'     => 'Este RUT ya está registrado.',
            'email.email'    => 'Ingresa un correo electrónico válido.',
            'website.url'    => 'Ingresa una URL válida (ej: https://empresa.cl).',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Normaliza el RUT antes de validar unicidad
        if ($this->rut) {
            $this->merge([
                'rut_raw' => ValidRut::clean($this->rut),
            ]);
        }
    }
}
