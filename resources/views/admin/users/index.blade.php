<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            Gestión de Usuarios
        </h2>
    </x-slot>


    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Botón Crear --}}
            <div class="flex justify-end mb-4">
                <a href="{{ route('admin.users.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700">
                    + Crear Usuario
                </a>
            </div>

            {{-- Tabla --}}
            <div class="bg-white shadow-xl rounded-lg overflow-hidden p-6">

                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b font-semibold text-gray-700">
                            <th class="py-2">Nombre</th>
                            <th class="py-2">Correo</th>
                            <th class="py-2">Rol</th>
                            <th class="py-2">Estado</th>
                            <th class="py-2">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-b">
                                <td class="py-3">
                                    {{ $user->nombres }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}
                                </td>

                                <td class="py-3">{{ $user->email }}</td>

                                <td class="py-3">
                                    {{ $user->role ? ucfirst($user->role->name) : 'Sin rol' }}
                                </td>

                                {{-- ESTADO --}}
                                <td class="py-3">
                                    @if($user->activo)
                                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                            Activo
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs rounded-full bg-gray-200 text-gray-700">
                                            Deshabilitado
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3 flex flex-wrap gap-2">

                                    {{-- Editar --}}
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                    class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700 hover:bg-blue-200">
                                        Editar
                                    </a>

                                    {{-- Habilitar / Deshabilitar --}}
                                    <form action="{{ route('admin.users.toggle', $user) }}" method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas cambiar el estado de este usuario?');">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                            class="text-sm px-3 py-1 rounded-full
                                                {{ $user->activo ? 'bg-[#9F2241] text-white hover:bg-[#691C32]'
                                                                : 'bg-gray-300 text-gray-800 hover:bg-gray-400' }}">
                                            {{ $user->activo ? 'Deshabilitar' : 'Habilitar' }}
                                        </button>
                                    </form>

                                    {{-- Eliminar --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('¿Seguro que deseas eliminar este usuario? Esta acción es irreversible.');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="text-sm px-3 py-1 rounded-full bg-red-100 text-red-700 hover:bg-red-200">
                                            Eliminar
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- Paginación --}}
                <div class="mt-4">
                    {{ $users->links() }}
                </div>

            </div>
        </div>
    </div>

</x-app-layout>
