<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl leading-tight text-white-800">
                    Gestión de Usuarios
                </h2>
                <p class="text-sm text-white-500 mt-1">
                    Administra usuarios por rol y controla su acceso al sistema.
                </p>
            </div>

            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-[#9F2241] text-white shadow-sm hover:bg-[#691C32] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9F2241] transition">
                <i class="fas fa-user-plus"></i>
                <span class="font-medium">Crear Usuario</span>
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-green-700">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="text-green-800 text-sm">
                            <p class="font-semibold">Operación realizada</p>
                            <p class="mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Orden visual por rol --}}
            @php
                $roleOrder = ['admin' => 1, 'validador' => 2, 'capturista' => 3, 'lector' => 4];

                $sortedUsers = $users->getCollection()
                    ->sortBy(function ($u) use ($roleOrder) {
                        $roleName = $u->role ? strtolower($u->role->name) : '';
                        return $roleOrder[$roleName] ?? 99;
                    });

                $groupedUsers = $sortedUsers->groupBy(function ($u) {
                    return $u->role ? strtolower($u->role->name) : 'sin rol';
                });

                $roleLabels = [
                    'admin' => 'Administradores',
                    'validador' => 'Validadores',
                    'capturista' => 'Capturistas',
                    'lector' => 'Lectores',
                    'sin rol' => 'Sin rol',
                ];

                $roleIcons = [
                    'admin' => 'fa-shield-halved',
                    'validador' => 'fa-clipboard-check',
                    'capturista' => 'fa-keyboard',
                    'lector' => 'fa-eye',
                    'sin rol' => 'fa-user',
                ];

                $roleAccent = [
                    'admin' => 'border-[#9F2241]/30 bg-[#9F2241]/5 text-[#9F2241]',
                    'validador' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
                    'capturista' => 'border-amber-200 bg-amber-50 text-amber-700',
                    'lector' => 'border-sky-200 bg-sky-50 text-sky-700',
                    'sin rol' => 'border-gray-200 bg-gray-50 text-gray-700',
                ];

                $orderedGroups = collect(['admin','validador','capturista','lector','sin rol'])
                    ->filter(fn($k) => $groupedUsers->has($k))
                    ->merge($groupedUsers->keys()->diff(['admin','validador','capturista','lector','sin rol']));
            @endphp

            {{-- Contenedor principal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-[#9F2241]">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Tarjetas de usuarios</h3>
                                <p class="text-sm text-gray-500">Ordenados por rol (de mayor a menor permisos).</p>
                            </div>
                        </div>

                        <div class="text-sm text-gray-500 flex items-center gap-2">
                            <i class="fas fa-layer-group"></i>
                            <span>Total: <span class="font-semibold text-gray-700">{{ $users->total() }}</span></span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-8">

                    {{-- Si no hay usuarios --}}
                    @if($sortedUsers->count() === 0)
                        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl bg-white border border-gray-200 flex items-center justify-center text-gray-500">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <h4 class="mt-4 font-semibold text-gray-800">Sin usuarios para mostrar</h4>
                            <p class="mt-1 text-sm text-gray-500">Crea el primer usuario para comenzar.</p>

                            <div class="mt-5">
                                <a href="{{ route('admin.users.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#9F2241] text-white shadow-sm hover:bg-[#691C32] transition">
                                    <i class="fas fa-user-plus"></i>
                                    Crear Usuario
                                </a>
                            </div>
                        </div>
                    @endif

                    {{-- Grupos por rol --}}
                    @foreach($orderedGroups as $roleKey)
                        @php
                            $group = $groupedUsers->get($roleKey, collect());
                            $label = $roleLabels[$roleKey] ?? ucfirst($roleKey);
                            $icon = $roleIcons[$roleKey] ?? 'fa-user';
                            $accent = $roleAccent[$roleKey] ?? 'border-gray-200 bg-gray-50 text-gray-700';
                        @endphp

                        @if($group->count() > 0)
                            <section class="space-y-4">

                                {{-- Header de grupo --}}
                                <div class="flex items-center justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl border flex items-center justify-center {{ $accent }}">
                                            <i class="fas {{ $icon }}"></i>
                                        </div>

                                        <div>
                                            <h4 class="text-base font-semibold text-gray-800">
                                                {{ $label }}
                                            </h4>
                                            <p class="text-sm text-gray-500">
                                                {{ $group->count() }} {{ $group->count() === 1 ? 'usuario' : 'usuarios' }}
                                            </p>
                                        </div>
                                    </div>

                                    <span class="text-xs font-semibold px-3 py-1 rounded-full border {{ $accent }}">
                                        {{ strtoupper($roleKey) }}
                                    </span>
                                </div>

                                {{-- Grid de tarjetas --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

                                    @foreach($group as $user)
                                        <div
                                            class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden"
                                            x-data="{ openToggle:false, openDelete:false }"
                                            @keydown.escape.window="openToggle=false; openDelete=false"
                                        >
                                            {{-- Top bar sutil --}}
                                            <div class="h-1 w-full bg-gradient-to-r from-[#9F2241] via-[#9F2241]/60 to-transparent"></div>

                                            <div class="p-5">
                                                {{-- Nombre + correo (sin recuadro/ícono a la izquierda) --}}
                                                <div class="space-y-2">
                                                    <p class="font-semibold text-gray-800 leading-snug break-words">
                                                        {{ $user->nombres }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}
                                                    </p>

                                                    <p class="text-sm text-gray-500 flex items-start gap-2 break-words">
                                                        <i class="fas fa-envelope text-gray-400 mt-0.5"></i>
                                                        <span class="break-words">{{ $user->email }}</span>
                                                    </p>

                                                    {{-- Estado SIEMPRE visible (en su propia línea) --}}
                                                    <div>
                                                        @if($user->activo)
                                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                                                <i class="fas fa-circle text-[10px]"></i>
                                                                Activo
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                                                                <i class="fas fa-ban"></i>
                                                                Deshabilitado
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Meta --}}
                                                <div class="mt-4 flex flex-wrap items-center gap-2">
                                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium border border-gray-200 bg-white text-gray-700">
                                                        <i class="fas fa-id-badge text-gray-400"></i>
                                                        {{ $user->role ? ucfirst($user->role->name) : 'Sin rol' }}
                                                    </span>

                                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium border border-gray-200 bg-gray-50 text-gray-700">
                                                        <i class="fas fa-hashtag text-gray-400"></i>
                                                        ID: {{ $user->id }}
                                                    </span>
                                                </div>

                                                {{-- Acciones --}}
                                                <div class="mt-5 pt-4 border-t border-gray-100 flex flex-wrap gap-2">
                                                    {{-- Editar --}}
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium bg-blue-50 text-blue-700 border border-blue-100 hover:bg-blue-100 transition">
                                                        <i class="fas fa-pen"></i>
                                                        Editar
                                                    </a>

                                                    {{-- Toggle (abre modal) --}}
                                                    <button type="button"
                                                            @click="openToggle=true"
                                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium border shadow-sm transition
                                                                {{ $user->activo ? 'bg-[#9F2241] text-white border-[#9F2241] hover:bg-[#691C32]' : 'bg-gray-200 text-gray-800 border-gray-300 hover:bg-gray-300' }}">
                                                        <i class="fas {{ $user->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                        {{ $user->activo ? 'Deshabilitar' : 'Habilitar' }}
                                                    </button>

                                                    {{-- Eliminar (abre modal) --}}
                                                    <button type="button"
                                                            @click="openDelete=true"
                                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition">
                                                        <i class="fas fa-trash"></i>
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- MODAL: Confirmar habilitar/deshabilitar --}}
                                            <div
                                                x-show="openToggle"
                                                x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                aria-modal="true"
                                                role="dialog"
                                            >
                                                {{-- Overlay --}}
                                                <div
                                                    class="absolute inset-0 bg-black/40"
                                                    @click="openToggle=false"
                                                ></div>

                                                {{-- Panel --}}
                                                <div
                                                    x-show="openToggle"
                                                    x-transition.opacity
                                                    x-transition.scale.95
                                                    class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 overflow-hidden"
                                                >
                                                    <div class="p-5 border-b border-gray-100">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="flex items-start gap-3">
                                                                <div class="h-10 w-10 rounded-xl flex items-center justify-center border
                                                                    {{ $user->activo ? 'bg-[#9F2241]/10 text-[#9F2241] border-[#9F2241]/20' : 'bg-gray-50 text-gray-700 border-gray-200' }}">
                                                                    <i class="fas {{ $user->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="font-semibold text-gray-800">
                                                                        {{ $user->activo ? 'Deshabilitar cuenta' : 'Habilitar cuenta' }}
                                                                    </h5>
                                                                    <p class="text-sm text-gray-500 mt-1">
                                                                        Esta acción cambiará el estado del usuario.
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <button type="button"
                                                                    @click="openToggle=false"
                                                                    class="h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition inline-flex items-center justify-center">
                                                                <i class="fas fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="p-5 space-y-3">
                                                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                                            <p class="text-sm text-gray-700 break-words">
                                                                <span class="font-semibold">Usuario:</span>
                                                                {{ $user->nombres }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}
                                                            </p>
                                                            <p class="text-sm text-gray-700 break-words mt-1">
                                                                <span class="font-semibold">Correo:</span> {{ $user->email }}
                                                            </p>
                                                        </div>

                                                        <p class="text-sm text-gray-600">
                                                            ¿Confirmas que deseas
                                                            <span class="font-semibold">{{ $user->activo ? 'deshabilitar' : 'habilitar' }}</span>
                                                            esta cuenta?
                                                        </p>
                                                    </div>

                                                    <div class="p-5 border-t border-gray-100 flex flex-col sm:flex-row gap-2 sm:justify-end">
                                                        <button type="button"
                                                                @click="openToggle=false"
                                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                                            <i class="fas fa-arrow-left"></i>
                                                            Cancelar
                                                        </button>

                                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button type="submit"
                                                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 rounded-xl text-white shadow-sm transition
                                                                        {{ $user->activo ? 'bg-[#9F2241] hover:bg-[#691C32]' : 'bg-gray-800 hover:bg-gray-900' }}">
                                                                <i class="fas {{ $user->activo ? 'fa-user-slash' : 'fa-user-check' }}"></i>
                                                                Sí, {{ $user->activo ? 'deshabilitar' : 'habilitar' }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- MODAL: Confirmar eliminar --}}
                                            <div
                                                x-show="openDelete"
                                                x-cloak
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                aria-modal="true"
                                                role="dialog"
                                            >
                                                {{-- Overlay --}}
                                                <div
                                                    class="absolute inset-0 bg-black/40"
                                                    @click="openDelete=false"
                                                ></div>

                                                {{-- Panel --}}
                                                <div
                                                    x-show="openDelete"
                                                    x-transition.opacity
                                                    x-transition.scale.95
                                                    class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 overflow-hidden"
                                                >
                                                    <div class="p-5 border-b border-gray-100">
                                                        <div class="flex items-start justify-between gap-3">
                                                            <div class="flex items-start gap-3">
                                                                <div class="h-10 w-10 rounded-xl flex items-center justify-center border bg-red-50 text-red-700 border-red-100">
                                                                    <i class="fas fa-triangle-exclamation"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="font-semibold text-gray-800">Eliminar usuario</h5>
                                                                    <p class="text-sm text-gray-500 mt-1">
                                                                        Esta acción es irreversible.
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <button type="button"
                                                                    @click="openDelete=false"
                                                                    class="h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-600 transition inline-flex items-center justify-center">
                                                                <i class="fas fa-xmark"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="p-5 space-y-3">
                                                        <div class="rounded-xl border border-red-100 bg-red-50/40 p-4">
                                                            <p class="text-sm text-gray-700 break-words">
                                                                Estás a punto de eliminar a:
                                                                <span class="font-semibold">
                                                                    {{ $user->nombres }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}
                                                                </span>
                                                            </p>
                                                            <p class="text-sm text-gray-700 break-words mt-1">
                                                                <span class="font-semibold">Correo:</span> {{ $user->email }}
                                                            </p>
                                                        </div>

                                                        <p class="text-sm text-gray-600">
                                                            ¿Confirmas que deseas <span class="font-semibold text-red-700">eliminar</span> este usuario?
                                                        </p>
                                                    </div>

                                                    <div class="p-5 border-t border-gray-100 flex flex-col sm:flex-row gap-2 sm:justify-end">
                                                        <button type="button"
                                                                @click="openDelete=false"
                                                                class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                                            <i class="fas fa-arrow-left"></i>
                                                            Cancelar
                                                        </button>

                                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="inline-flex w-full sm:w-auto items-center justify-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white shadow-sm hover:bg-red-700 transition">
                                                                <i class="fas fa-trash"></i>
                                                                Sí, eliminar
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    @endforeach

                                </div>
                            </section>
                        @endif
                    @endforeach

                    {{-- Paginación --}}
                    <div class="pt-2">
                        {{ $users->links() }}
                    </div>

                </div>
            </div>

        </div>
    </div>

</x-app-layout>
