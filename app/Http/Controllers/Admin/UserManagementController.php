<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Institucion;
use App\Models\Subdependencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Configuración de límite de usuarios por rol.
     * Clave = nombre del rol (columna name en roles), valor = máximo permitido.
     */
    protected array $maxUsersByRole = [
        'admin' => 3,
        'validador' => 4,
        'capturista' => 4,
    ];

    // LISTADO DE USUARIOS
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::with(['role','institucion','subdependencia'])->paginate(10)
        ]);
    }

    // FORMULARIO CREAR
    public function create()
    {
        return view('admin.users.create', [
            'roles'          => Role::all(),
            'instituciones'  => Institucion::all(),
            'subdependencias'=> Subdependencia::with('institucion')->orderBy('nombre')->get(),
        ]);
    }

    // GUARDAR
    public function store(Request $request)
    {
        $request->validate([
            'nombres'            => 'required|string|max:255',
            'apellido_paterno'   => 'required|string|max:255',
            'apellido_materno'   => 'nullable|string|max:255',

            'curp'               => [
                'required',
                'string',
                'size:18',
                // Patrón simplificado de CURP, acepta mayúsculas y minúsculas
                'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/i',
                'unique:users,curp',
            ],

            'email'              => 'required|email|unique:users,email',

            'password'           => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',        // Al menos una mayúscula
                'regex:/[a-z]/',        // Al menos una minúscula
                'regex:/[0-9]/',        // Al menos un número
                'regex:/[^A-Za-z0-9]/', // Al menos un carácter especial
                'confirmed',            // <-- CUADRO DE CONFIRMACIÓN
            ],

            'role_id'            => 'required|exists:roles,id',
            'institucion_id'     => 'required|exists:instituciones,id',
            'subdependencia_id'  => 'nullable|exists:subdependencias,id',
        ]);

        // Obtener rol para revisar límite
        $role = Role::findOrFail($request->role_id);

        if (isset($this->maxUsersByRole[$role->name])) {
            $currentCount = User::where('role_id', $role->id)->count();

            if ($currentCount >= $this->maxUsersByRole[$role->name]) {
                return back()
                    ->withErrors([
                        'role_id' => 'No es posible crear más usuarios con el rol "' . ucfirst($role->name) .
                                    '". Límite permitido: ' . $this->maxUsersByRole[$role->name] . '.',
                    ])
                    ->withInput();
            }
        }

        User::create([
            'nombres'           => $request->nombres,
            'apellido_paterno'  => $request->apellido_paterno,
            'apellido_materno'  => $request->apellido_materno,
            'curp'              => $request->curp,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role_id'           => $request->role_id,
            'institucion_id'    => $request->institucion_id,
            'subdependencia_id' => $request->subdependencia_id,
            'activo'            => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    // FORMULARIO EDITAR
    public function edit(User $user)
    {
        return view('admin.users.edit', [
            'user'            => $user,
            'roles'           => Role::all(),
            'instituciones'   => Institucion::all(),
            'subdependencias' => Subdependencia::with('institucion')->orderBy('nombre')->get(),
        ]);
    }

    // ACTUALIZAR
    public function update(Request $request, User $user)
    {
        $request->validate([
            'nombres'            => 'required|string|max:255',
            'apellido_paterno'   => 'required|string|max:255',
            'apellido_materno'   => 'nullable|string|max:255',

            'curp'               => [
                'required',
                'string',
                'size:18',
                'regex:/^[A-Z]{4}[0-9]{6}[HM][A-Z]{5}[0-9A-Z][0-9]$/i',
                'unique:users,curp,' . $user->id,
            ],

            'email'              => 'required|email|unique:users,email,' . $user->id,

            'password'           => [
                'nullable',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[^A-Za-z0-9]/',
                'confirmed',
            ],

            'role_id'            => 'required|exists:roles,id',
            'institucion_id'     => 'required|exists:instituciones,id',
            'subdependencia_id'  => 'nullable|exists:subdependencias,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        if (isset($this->maxUsersByRole[$role->name])) {
            $currentCount = User::where('role_id', $role->id)->count();

            if ($user->role_id != $role->id && $currentCount >= $this->maxUsersByRole[$role->name]) {
                return back()
                    ->withErrors([
                        'role_id' => 'No es posible asignar el rol "' . ucfirst($role->name) .
                                    '" porque ya alcanzó el límite de ' . $this->maxUsersByRole[$role->name] . ' usuarios.',
                    ])
                    ->withInput();
            }
        }

        $data = [
            'nombres'           => $request->nombres,
            'apellido_paterno'  => $request->apellido_paterno,
            'apellido_materno'  => $request->apellido_materno,
            'curp'              => $request->curp,
            'email'             => $request->email,
            'role_id'           => $request->role_id,
            'institucion_id'    => $request->institucion_id,
            'subdependencia_id' => $request->subdependencia_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

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

    // HABILITAR / DESHABILITAR USUARIOS
    public function toggleStatus(User $user)
    {
        $user->activo = ! $user->activo;
        $user->save();

        return redirect()
            ->route('admin.users.index')
            ->with(
                'success',
                $user->activo
                    ? 'Usuario habilitado correctamente.'
                    : 'Usuario deshabilitado correctamente.'
            );
    }
}