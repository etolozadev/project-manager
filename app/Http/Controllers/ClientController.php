<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('id', 'desc')->get();
        return Inertia::render('clients', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:150',
            'rut' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);
        Client::create($data);
        return redirect()->route('clients');
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'nullable|email|max:150',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:150',
            'rut' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:255',
        ]);
        $client->update($data);
        return redirect()->route('clients');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('clients');
    }
}
