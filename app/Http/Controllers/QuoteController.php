<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuoteRequest;
use App\Http\Requests\UpdateQuoteRequest;
use App\Models\Client;
use App\Models\Quote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index()
    {
        $quotes = Quote::with('client')
            ->when(request('search'), fn ($q) => $q->search(request('search')))
            ->when(request('status'), fn ($q) => $q->byStatus(request('status')))
            ->when(request('client_id'), fn ($q) => $q->where('client_id', request('client_id')))
            ->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($q) => $this->quoteResource($q));

        $clients = Client::active()->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Quotes/Index', [
            'quotes'  => $quotes,
            'clients' => $clients,
            'filters' => request()->only(['search', 'status', 'client_id']),
            'summary' => [
                'total'    => Quote::count(),
                'draft'    => Quote::where('status', 'draft')->count(),
                'sent'     => Quote::where('status', 'sent')->count(),
                'approved' => Quote::where('status', 'approved')->count(),
            ],
        ]);
    }

    public function create()
    {
        $clients = Client::active()->orderBy('name')->get()->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $c->name,
            'rut'  => $c->rut,
        ]);

        return Inertia::render('Quotes/Create', [
            'clients'      => $clients,
            'defaultTerms' => config('app.quote_terms', 'Los precios indicados son en pesos chilenos (CLP). Esta cotización tiene una vigencia de 30 días desde su emisión. El inicio del proyecto queda sujeto a la aprobación formal y al pago del anticipo acordado.'),
        ]);
    }

    public function store(StoreQuoteRequest $request)
    {
        $quote = Quote::create([
            ...$request->safe()->except('items'),
            'created_by'   => auth()->id(),
            'quote_number' => Quote::generateNumber(),
        ]);

        foreach ($request->items as $i => $itemData) {
            $quote->items()->create([...$itemData, 'order' => $i]);
        }

        $quote->recalculateTotals();

        return redirect()->route('quotes.show', $quote)
            ->with('success', "Cotización {$quote->quote_number} creada correctamente.");
    }

    public function show(Quote $quote)
    {
        $quote->load(['client', 'creator', 'project', 'items']);

        return Inertia::render('Quotes/Show', [
            'quote' => $this->quoteDetailResource($quote),
        ]);
    }

    public function edit(Quote $quote)
    {
        abort_if(! $quote->is_editable, 403, 'Solo se pueden editar cotizaciones en borrador.');

        $quote->load('items');
        $clients = Client::active()->orderBy('name')->get()->map(fn ($c) => [
            'id'   => $c->id,
            'name' => $c->name,
            'rut'  => $c->rut,
        ]);

        return Inertia::render('Quotes/Edit', [
            'quote'   => $this->quoteDetailResource($quote),
            'clients' => $clients,
        ]);
    }

    public function update(UpdateQuoteRequest $request, Quote $quote)
    {
        abort_if(! $quote->is_editable, 403, 'Solo se pueden editar cotizaciones en borrador.');

        $quote->update($request->safe()->except('items'));

        // Reemplazar items: eliminar los actuales y recrear
        $quote->items()->delete();
        foreach ($request->items as $i => $itemData) {
            $quote->items()->create([...$itemData, 'order' => $i]);
        }

        $quote->recalculateTotals();

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'Cotización actualizada correctamente.');
    }

    public function destroy(Quote $quote)
    {
        abort_if($quote->status === 'approved', 403, 'No se puede eliminar una cotización aprobada.');

        $quote->delete();

        return redirect()->route('quotes.index')
            ->with('success', 'Cotización eliminada correctamente.');
    }

    // ── Acciones de estado ──────────────────────────────────────

    public function markAsSent(Quote $quote)
    {
        abort_if($quote->status !== 'draft', 403, 'Solo se pueden enviar cotizaciones en borrador.');

        $quote->markAsSent();

        return back()->with('success', "Cotización {$quote->quote_number} marcada como enviada.");
    }

    public function approve(Quote $quote)
    {
        abort_if($quote->status !== 'sent', 403, 'Solo se pueden aprobar cotizaciones enviadas.');

        $project = $quote->approve();

        return redirect()->route('projects.show', $project)
            ->with('success', "¡Cotización aprobada! Proyecto {$project->code} creado automáticamente.");
    }

    public function reject(Quote $quote)
    {
        abort_if(! in_array($quote->status, ['draft', 'sent']), 403, 'No se puede rechazar en este estado.');

        $quote->reject();

        return back()->with('success', "Cotización {$quote->quote_number} marcada como rechazada.");
    }

    public function duplicate(Quote $quote)
    {
        $newQuote = Quote::create([
            'client_id'    => $quote->client_id,
            'created_by'   => auth()->id(),
            'quote_number' => Quote::generateNumber(),
            'title'        => "Copia de {$quote->title}",
            'status'       => 'draft',
            'currency'     => $quote->currency,
            'tax_rate'     => $quote->tax_rate,
            'tax_included' => $quote->tax_included,
            'notes'        => $quote->notes,
            'terms'        => $quote->terms,
        ]);

        foreach ($quote->items as $item) {
            $newQuote->items()->create([
                'description' => $item->description,
                'detail'      => $item->detail,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'order'       => $item->order,
            ]);
        }

        $newQuote->recalculateTotals();

        return redirect()->route('quotes.edit', $newQuote)
            ->with('success', "Cotización duplicada como {$newQuote->quote_number}.");
    }

    // ── PDF ─────────────────────────────────────────────────────

    public function downloadPdf(Quote $quote)
    {
        $quote->load(['client', 'creator', 'items']);

        $pdf = Pdf::loadView('pdf.quote', compact('quote'))
            ->setPaper('letter', 'portrait')
            ->setOption('dpi', 150)
            ->setOption('enable_html5_parser', true);

        $filename = "{$quote->quote_number}.pdf";

        return $pdf->download($filename);
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function quoteResource(Quote $q): array
    {
        return [
            'id'              => $q->id,
            'quote_number'    => $q->quote_number,
            'title'           => $q->title,
            'status'          => $q->status,
            'status_name'     => $q->status_name,
            'status_color'    => $q->status_color,
            'currency'        => $q->currency,
            'formatted_total' => $q->formatted_total,
            'valid_until'     => $q->valid_until?->format('d/m/Y'),
            'is_editable'     => $q->is_editable,
            'client_name'     => $q->client->name,
            'created_at'      => $q->created_at->format('d/m/Y'),
        ];
    }

    private function quoteDetailResource(Quote $q): array
    {
        return [
            'id'                  => $q->id,
            'quote_number'        => $q->quote_number,
            'title'               => $q->title,
            'status'              => $q->status,
            'status_name'         => $q->status_name,
            'status_color'        => $q->status_color,
            'currency'            => $q->currency,
            'tax_rate'            => $q->tax_rate,
            'tax_included'        => $q->tax_included,
            'subtotal'            => $q->subtotal,
            'tax_amount'          => $q->tax_amount,
            'total'               => $q->total,
            'formatted_subtotal'  => $q->formatted_subtotal,
            'formatted_tax'       => $q->formatted_tax_amount,
            'formatted_total'     => $q->formatted_total,
            'valid_until'         => $q->valid_until?->format('Y-m-d'),
            'valid_until_display' => $q->valid_until?->format('d/m/Y'),
            'notes'               => $q->notes,
            'terms'               => $q->terms,
            'is_editable'         => $q->is_editable,
            'sent_at'             => $q->sent_at?->format('d/m/Y H:i'),
            'approved_at'         => $q->approved_at?->format('d/m/Y H:i'),
            'rejected_at'         => $q->rejected_at?->format('d/m/Y H:i'),
            'created_at'          => $q->created_at->format('d/m/Y'),
            'client_id'           => $q->client_id,
            'client' => [
                'id'             => $q->client->id,
                'name'           => $q->client->name,
                'rut'            => $q->client->rut,
                'email'          => $q->client->email,
                'contact_person' => $q->client->contact_person,
                'city'           => $q->client->city,
                'region'         => $q->client->region,
            ],
            'project' => $q->project ? [
                'id'   => $q->project->id,
                'code' => $q->project->code,
                'name' => $q->project->name,
            ] : null,
            'items' => $q->items->map(fn ($item) => [
                'id'          => $item->id,
                'description' => $item->description,
                'detail'      => $item->detail,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'subtotal'    => $item->subtotal,
            ])->values()->all(),
        ];
    }
}
