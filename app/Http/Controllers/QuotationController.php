<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuotationController extends Controller
{
    public function index()
    {
        $quotations = Quotation::with('client')->orderBy('id', 'desc')->get();
        $clients = Client::all(['id', 'name']);
        return Inertia::render('quotes/index', [
            'quotations' => $quotations,
            'clients' => $clients,
        ]);
    }

    public function create()
    {
        $clients = Client::all(['id', 'name']);
        return Inertia::render('quotes/create', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'delivery_date' => 'nullable|date',
            'quote_items' => 'required|array',
            'quote_items.*.description' => 'required|string',
            'quote_items.*.quantity' => 'required|integer|min:1',
            'quote_items.*.unit_price' => 'required|numeric|min:0',
            'quote_items.*.total' => 'required|numeric|min:0',
            'discount' => 'nullable|integer|min:0|max:100',
            'tax' => 'nullable|integer|min:0|max:100',
        ]);

        $subtotal = array_sum(array_map(fn($item) => $item['total'], $data['quote_items']));
        $discount = $data['discount'] ?? 0;
        $tax = $data['tax'] ?? 0;
        $discountAmount = $subtotal * ($discount / 100);
        $taxedAmount = ($subtotal - $discountAmount) * ($tax / 100);
        $total = $subtotal - $discountAmount + $taxedAmount;

        $quotation = Quotation::create([
            'client_id' => $data['client_id'],
            'title' => $data['title'],
            'description' => $data['description'],
            'delivery_date' => $data['delivery_date'],
            'subtotal' => $subtotal,
            'discount' => $discount,
            'tax' => $tax,
            'total' => $total,
        ]);

        foreach ($data['quote_items'] as $itemData) {
            $quotation->quote_items()->create($itemData);
        }

        return redirect()->route('quotes.show', $quotation->id);
    }

    public function show(Quotation $quotation)
    {
        $quotation->load('client', 'quote_items');
        return Inertia::render('quotes/show', [
            'quote' => $quotation,
        ]);
    }

    public function update(Request $request, Quotation $quotation)
    {
        $data = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,approved,rejected',
            'total_amount' => 'required|numeric|min:0',
            'delivery_date' => 'nullable|date',
        ]);

        $quotation->update($data);

        return redirect()->route('quotes.show', $quotation->id);
    }

    public function destroy(Quotation $quotation)
    {
        $quotation->delete();
        return redirect()->route('quotes');
    }
}
