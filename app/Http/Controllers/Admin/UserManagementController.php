<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Institucion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    // LISTADO DE USUARIOS
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with(['role','institucion'])->paginate(10)
        ]);
    }

    // FORMULARIO CREAR
    public function create()
    {
        return view('admin.users.create', [
            'roles' => Role::all(),
            'instituciones' => Institucion::all()
        ]);
    }

    // GUARDAR
    public function store(Request $request)
    {
        $request->validate([
            'nombres'            => 'required|string|max:255',
            'apellido_paterno'   => 'required|string|max:255',
            'apellido_materno'   => 'nullable|string|max:255',
            'email'              => 'required|email|unique:users,email',
            'password'           => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'role_id'            => 'required|exists:roles,id',
            'institucion_id'     => 'required|exists:instituciones,id',
        ]);

        User::create([
            'nombres'          => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email'            => $request->email,
            'password'         => Hash::make($request->password),
            'role_id'          => $request->role_id,
            'institucion_id'   => $request->institucion_id,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // FORMULARIO EDITAR
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user'          => $user,
            'roles'         => Role::all(),
            'instituciones' => Institucion::all()
        ]);
    }

    // ACTUALIZAR
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nombres'            => 'required|string|max:255',
            'apellido_paterno'   => 'required|string|max:255',
            'apellido_materno'   => 'nullable|string|max:255',
            'email'              => 'required|email|unique:users,email,' . $user->id,
            'password'           => [
                'nullable',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
            ],
            'role_id'            => 'required|exists:roles,id',
            'institucion_id'     => 'required|exists:instituciones,id',
        ]);

        $user->update([
            'nombres'          => $request->nombres,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'email'            => $request->email,
            'role_id'          => $request->role_id,
            'institucion_id'   => $request->institucion_id,
            'password'         => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    // ELIMINAR
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado.');
    }
}
