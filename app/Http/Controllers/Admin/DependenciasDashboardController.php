<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Subdependencia;
use App\Models\User;
use Illuminate\Http\Request;

class DependenciasDashboardController extends Controller
{
    public function index()
    {
        $rolOrden = ['admin', 'capturista', 'validador', 'lector'];

        $instituciones = Institucion::with([
            'subdependencias.users.role',
            'subdependencias.users.institucion',
            'users.role',
        ])
        ->orderBy('orden')     // orden personalizado
        ->orderBy('nombre')
        ->get();

        foreach ($instituciones as $inst) {

            // Usuarios sin subdependencia (pero pertenecen a la institución)
            $inst->usuariosSinSubdependencia = $inst->users
                ->whereNull('subdependencia_id')
                ->sortBy(function ($u) use ($rolOrden) {
                    $nombreRol = $u->role?->nombre ?? $u->role?->name ?? '';
                    $pos = array_search($nombreRol, $rolOrden, true);
                    return $pos === false ? 999 : $pos;
                })
                ->values();

            // Ordenar usuarios dentro de cada subdependencia
            $inst->subdependencias->each(function ($sub) use ($rolOrden) {
                $sub->users = $sub->users
                    ->sortBy(function ($u) use ($rolOrden) {
                        $nombreRol = $u->role?->nombre ?? $u->role?->name ?? '';
                        $pos = array_search($nombreRol, $rolOrden, true);
                        return $pos === false ? 999 : $pos;
                    })
                    ->values();
            });
        }

        return view('admin.dependencias.index', compact('instituciones'));
    }

    public function asignar(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subdependencia_id' => 'required|exists:subdependencias,id',
        ]);

        $user = User::findOrFail($data['user_id']);
        $sub = Subdependencia::findOrFail($data['subdependencia_id']);

        // Opcional pero recomendado: asegurar que usuario e institución coincidan
        if ((int)$user->institucion_id !== (int)$sub->institucion_id) {
            return back()->with('error', 'La subdependencia no pertenece a la institución del usuario.');
        }

        $user->subdependencia_id = $sub->id;
        $user->save();

        return back()->with('success', 'Usuario asignado a la subdependencia correctamente.');
    }

    public function quitar(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($data['user_id']);
        $user->subdependencia_id = null;
        $user->save();

        return back()->with('success', 'Usuario removido de la subdependencia.');
    }

    public function asignarSubdependencia(Request $request, User $user)
    {
        $data = $request->validate([
            'subdependencia_id' => ['required','exists:subdependencias,id'],
        ]);

        $sub = Subdependencia::findOrFail($data['subdependencia_id']);

        // seguridad: solo permitir si el usuario pertenece a la misma institución
        if ((int)$user->institucion_id !== (int)$sub->institucion_id) {
            abort(422, 'La subdependencia no corresponde a la institución del usuario.');
        }

        $user->subdependencia_id = $sub->id;
        $user->save();

        return back()->with('success', 'Usuario asignado a la subdependencia correctamente.');
    }

    public function quitarSubdependencia(User $user)
    {
        $user->subdependencia_id = null;
        $user->save();

        return back()->with('success', 'Usuario quitado de la subdependencia (queda solo en la institución).');
    }
}
