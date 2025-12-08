<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
public function store(LoginRequest $request)
{
    // Intenta autenticar al usuario
    try {
        $request->authenticate();
    } catch (\Exception $e) {
        return back()->withErrors([
            'email' => 'El correo electrónico o la contraseña son incorrectos.',
        ]);
    }

    $request->session()->regenerate();

    $user = auth()->user();

    // Si por alguna razón no se obtuvo usuario autenticado
    if (!$user) {
        Auth::logout();
        return back()->withErrors([
            'email' => 'No se pudo iniciar sesión.',
        ]);
    }

    // Si no tiene rol asignado
    if (!$user->role) {
        Auth::logout();
        return back()->withErrors([
            'email' => 'Tu cuenta no tiene un rol asignado.',
        ]);
    }
    // Cuenta deshabilitada
    if (!$user->activo) {
        Auth::logout();
        return back()->withErrors([
            'email' => 'Tu usuario se encuentra deshabilitado. Por favor comunícate con la institución para reactivarlo.',
        ]);
    }
    // Redirecciones según rol
    if ($user->role->name === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    if ($user->role->name === 'SESESP') {
        return redirect()->route('dependencia.dashboard');
    }

    if ($user->role->name === 'capturista') {
        return redirect()->route('capturista.dashboard');
    }

    if ($user->role->name === 'validador') {
        return redirect()->route('validador.dashboard');
    }

    if ($user->role->name === 'lector') {
        return redirect()->route('lector.dashboard');
    }
    return redirect()->route('dashboard');
}

    
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
