<?php

use Illuminate\Support\Facades\Auth;

if (! function_exists('dashboard_route')) {
    function dashboard_route(): string
    {
        $user = Auth::user();

        if (! $user || ! $user->role) {
            return route('login');
        }

        return match ($user->role->name) {
            'admin'       => route('admin.dashboard'),
            'capturista'  => route('capturista.dashboard'),
            'dependencia' => route('dependencia.dashboard'),
            'validador'   => route('validador.dashboard'),
            'lector'      => route('lector.dashboard'),
            default       => route('login'),
        };
    }
}
