<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => ['required', 'exists:projects,id'],
            'quote_id'     => ['nullable', 'exists:quotes,id'],
            'amount'       => ['required', 'integer', 'min:1'],
            'currency'     => ['required', 'in:CLP,USD'],
            'payment_date' => ['required', 'date'],
            'method'       => ['required', 'in:transfer,cash,check,card,other'],
            'reference'    => ['nullable', 'string', 'max:255'],
            'notes'        => ['nullable', 'string'],
        ], [
            'amount.required'       => 'El monto es obligatorio.',
            'amount.min'            => 'El monto debe ser mayor a 0.',
            'payment_date.required' => 'La fecha de pago es obligatoria.',
        ]);

        // Limpiar formato del monto si viene con puntos
        $validated['amount'] = (int) preg_replace('/[^0-9]/', '', $request->amount);
        $validated['created_by'] = auth()->id();

        Payment::create($validated);

        return redirect()->route('projects.show', $request->project_id)
            ->with('success', 'Pago registrado correctamente.');
    }

    public function destroy(Payment $payment)
    {
        $projectId = $payment->project_id;
        $payment->delete();

        return redirect()->route('projects.show', $projectId)
            ->with('success', 'Pago eliminado correctamente.');
    }
}
