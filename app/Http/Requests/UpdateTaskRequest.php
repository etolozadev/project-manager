<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => ['required', 'string', 'max:255'],
            'description'     => ['nullable', 'string'],
            'category'        => ['required', 'in:development,design,server,testing,documentation,meeting,other'],
            'status'          => ['required', 'in:backlog,in_progress,review,done'],
            'priority'        => ['required', 'in:low,medium,high,critical'],
            'due_date'        => ['nullable', 'date'],
            'estimated_hours' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'actual_hours'    => ['nullable', 'integer', 'min:0', 'max:9999'],
            'assigned_to'     => ['nullable', 'exists:users,id'],
            'notes'           => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'El título es obligatorio.',
            'category.required' => 'Selecciona una categoría.',
            'status.required'   => 'Selecciona un estado.',
            'priority.required' => 'Selecciona la prioridad.',
        ];
    }
}
