<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Dependencias
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 border border-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-[#691C32]">Listado por Dependencias</h1>
                        <p class="text-gray-600 text-sm">
                            Visualiza usuarios agrupados por institución y subdependencia.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('admin.instituciones.index') }}"
                           class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                            Gestionar Instituciones
                        </a>

                        <a href="{{ route('admin.subdependencias.index') }}"
                           class="px-4 py-2 rounded-lg bg-white text-[#9F2241] font-semibold border border-[#9F2241] hover:bg-[#9F2241]/10 transition">
                            Gestionar Subdependencias
                        </a>
                    </div>
                </div>

                <div class="space-y-8">
                    @forelse($instituciones as $inst)

                        <div class="border border-gray-200 rounded-2xl overflow-hidden">
                            <div class="bg-[#691C32] text-white px-6 py-4 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-sm opacity-90">Dependencia</span>
                                    <span class="text-lg font-bold">{{ $inst->nombre }}</span>
                                </div>

                                <span class="text-xs bg-white/10 px-3 py-1 rounded-full">
                                    {{ $inst->subdependencias->count() }} subdependencias
                                </span>
                            </div>

                            <div class="p-6 space-y-6">

                                {{-- SUBDEPENDENCIAS --}}
                                @forelse($inst->subdependencias as $sub)
                                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4">
                                        <div class="flex items-center justify-between mb-4">
                                            <div>
                                                <h3 class="text-lg font-semibold text-[#9F2241]">
                                                    {{ $sub->nombre }}
                                                </h3>
                                                <p class="text-xs text-gray-500">Subdependencia</p>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <span class="text-xs bg-white border border-gray-200 px-3 py-1 rounded-full">
                                                    {{ $sub->users->count() }} usuarios
                                                </span>

                                                {{-- + Asignar usuario a esta subdependencia --}}
                                                <form method="POST" action="{{ route('admin.dependencias.asignar') }}" class="flex items-center gap-2">
                                                    @csrf
                                                    <input type="hidden" name="subdependencia_id" value="{{ $sub->id }}">

                                                    <select name="user_id"
                                                            class="rounded-lg border-gray-300 text-sm"
                                                            @if(($inst->usuariosSinSubdependencia?->count() ?? 0) === 0) disabled @endif>
                                                        <option value="" selected disabled>
                                                            + Asignar usuario…
                                                        </option>

                                                        @foreach(($inst->usuariosSinSubdependencia ?? collect()) as $u)
                                                            <option value="{{ $u->id }}">
                                                                {{ $u->nombres }} ({{ $u->role?->nombre ?? 'sin rol' }})
                                                            </option>
                                                        @endforeach
                                                    </select>

                                                    <button type="submit"
                                                            class="px-3 py-2 rounded-lg bg-[#9F2241] text-white text-sm font-semibold hover:bg-[#691C32] transition"
                                                            @if(($inst->usuariosSinSubdependencia?->count() ?? 0) === 0) disabled @endif>
                                                        Agregar Usuario
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Tabla usuarios --}}
                                        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                                            <table class="w-full text-left">
                                                <thead>
                                                    <tr class="bg-[#9F2241]/10 text-[#691C32] font-semibold">
                                                        <th class="py-3 px-4">Usuario</th>
                                                        <th class="py-3 px-4">Correo</th>
                                                        <th class="py-3 px-4">Rol</th>
                                                        <th class="py-3 px-4">Estado</th>
                                                        <th class="py-3 px-4">Acciones</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @forelse($sub->users as $u)
                                                        <tr class="border-t">
                                                            <td class="py-3 px-4">
                                                                <div class="font-semibold text-gray-800">
                                                                    {{ $u->nombres }} {{ $u->apellido_paterno }} {{ $u->apellido_materno }}
                                                                </div>
                                                            </td>

                                                            <td class="py-3 px-4 text-gray-700">
                                                                {{ $u->email }}
                                                            </td>

                                                            <td class="py-3 px-4">
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241]">
                                                                    {{ ucfirst($u->role?->nombre ?? $u->role?->name ?? 'sin rol') }}
                                                                </span>
                                                            </td>

                                                            <td class="py-3 px-4">
                                                                @if($u->activo)
                                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                                        Activo
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                                                        Deshabilitado
                                                                    </span>
                                                                @endif
                                                            </td>

                                                            <td class="py-3 px-4">
                                                                <form method="POST" action="{{ route('admin.dependencias.quitar') }}">
                                                                @csrf
                                                                <input type="hidden" name="user_id" value="{{ $u->id }}">
                                                                <button type="submit"
                                                                class="px-3 py-2 rounded-lg bg-[#F5B027] text-white text-sm font-semibold hover:bg-[#691C32] transition">Quitar</button>
                                                            </form>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="4" class="py-6 px-4 text-center text-gray-500">
                                                                No hay usuarios asignados a esta subdependencia.
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-6 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                                        Esta institución aún no tiene subdependencias registradas.
                                    </div>
                                @endforelse

                                {{-- USUARIOS SIN SUBDEPENDENCIA (SIEMPRE SE MUESTRAN SI HAY) --}}
                                <div class="bg-white border border-gray-200 rounded-xl p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <h3 class="text-lg font-semibold text-[#691C32]">Usuarios sin subdependencia</h3>
                                            <p class="text-xs text-gray-500">
                                                Usuarios registrados en la dependencia pero no asignados a una subdependencia.
                                            </p>
                                        </div>

                                        <span class="text-xs bg-gray-100 border border-gray-200 px-3 py-1 rounded-full">
                                            {{ ($inst->usuariosSinSubdependencia?->count() ?? 0) }} usuarios
                                        </span>
                                    </div>

                                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                                        <table class="w-full text-left">
                                            <thead>
                                                <tr class="bg-gray-50 text-gray-700 font-semibold">
                                                    <th class="py-3 px-4">Usuario</th>
                                                    <th class="py-3 px-4">Correo</th>
                                                    <th class="py-3 px-4">Rol</th>
                                                    <th class="py-3 px-4">Estado</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @forelse(($inst->usuariosSinSubdependencia ?? collect()) as $u)
                                                    <tr class="border-t">
                                                        <td class="py-3 px-4 font-semibold text-gray-800">
                                                            {{ $u->nombres }} {{ $u->apellido_paterno }} {{ $u->apellido_materno }}
                                                        </td>
                                                        <td class="py-3 px-4 text-gray-700">{{ $u->email }}</td>
                                                        <td class="py-3 px-4">
                                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241]">
                                                                {{ ucfirst($u->role?->nombre ?? $u->role?->name ?? 'sin rol') }}
                                                            </span>
                                                        </td>
                                                        <td class="py-3 px-4">
                                                            @if($u->activo)
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Activo</span>
                                                            @else
                                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Deshabilitado</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="py-6 px-4 text-center text-gray-500">
                                                            No hay usuarios pendientes de subdependencia.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>

                    @empty
                        <div class="p-6 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                            No hay instituciones registradas todavía.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
