<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Inertia\Inertia;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     *
     * Usa Inertia::location() en lugar de redirect() para forzar una
     * redirección completa del navegador. Si se usa redirect() normal,
     * Inertia intercepta la respuesta y muestra la página de login
     * (Blade HTML) como un modal de error sobre el dashboard.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // location() envía header X-Inertia-Location → el cliente hace
        // window.location.href en lugar de fetch XHR, evitando el conflicto.
        return Inertia::location('/');
    }
}
