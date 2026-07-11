<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => ['required', 'exists:projects,id'],
            'category'     => ['required', 'in:hosting,domain,license,subcontract,tools,travel,other'],
            'description'  => ['required', 'string', 'max:255'],
            'amount'       => ['required', 'integer', 'min:1'],
            'currency'     => ['required', 'in:CLP,USD'],
            'expense_date' => ['required', 'date'],
            'notes'        => ['nullable', 'string'],
        ], [
            'description.required'  => 'La descripción es obligatoria.',
            'amount.required'       => 'El monto es obligatorio.',
            'expense_date.required' => 'La fecha es obligatoria.',
        ]);

        $validated['amount']     = (int) preg_replace('/[^0-9]/', '', $request->amount);
        $validated['created_by'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('projects.show', $request->project_id)
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function destroy(Expense $expense)
    {
        $projectId = $expense->project_id;
        $expense->delete();

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Gasto eliminado correctamente.');
    }
}
