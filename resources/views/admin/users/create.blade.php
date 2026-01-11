{{-- resources/views/admin/users/create.blade.php --}}

<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl leading-tight text-white">
                    Crear nuevo usuario
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Completa la información para registrar un usuario y asignarle rol e institución.
                </p>
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white/15 transition">
                <i class="fas fa-arrow-left"></i>
                Regresar
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- Card principal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-[#9F2241]">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Información del usuario</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Los campos marcados son obligatorios. Puedes asignar subdependencia de forma opcional.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-8">
                        @csrf

                        {{-- Sección: Datos personales --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/15">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <h4 class="font-semibold text-gray-800">Datos personales</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Nombres --}}
                                <div class="md:col-span-2">
                                    <label for="nombres" class="block text-sm font-semibold text-gray-700">
                                        Nombres <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" name="nombres" id="nombres"
                                           value="{{ old('nombres') }}"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:border-[#9F2241] focus:ring-[#9F2241]" />
                                    @error('nombres')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido paterno --}}
                                <div>
                                    <label for="apellido_paterno" class="block text-sm font-semibold text-gray-700">
                                        Apellido paterno <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" name="apellido_paterno" id="apellido_paterno"
                                           value="{{ old('apellido_paterno') }}"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:border-[#9F2241] focus:ring-[#9F2241]" />
                                    @error('apellido_paterno')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido materno --}}
                                <div>
                                    <label for="apellido_materno" class="block text-sm font-semibold text-gray-700">
                                        Apellido materno
                                    </label>
                                    <input type="text" name="apellido_materno" id="apellido_materno"
                                           value="{{ old('apellido_materno') }}"
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:border-[#9F2241] focus:ring-[#9F2241]" />
                                    @error('apellido_materno')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Sección: Acceso --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/15">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <h4 class="font-semibold text-gray-800">Acceso</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700">
                                        Correo electrónico <span class="text-red-600">*</span>
                                    </label>
                                    <input type="email" name="email" id="email"
                                           value="{{ old('email') }}"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:border-[#9F2241] focus:ring-[#9F2241]" />
                                    @error('email')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Rol --}}
                                <div>
                                    <label for="role_id" class="block text-sm font-semibold text-gray-700">
                                        Rol de usuario <span class="text-red-600">*</span>
                                    </label>
                                    <select name="role_id" id="role_id"
                                            required
                                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                   focus:border-[#9F2241] focus:ring-[#9F2241]">
                                        <option value="" disabled selected>Seleccione un rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}">
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Password --}}
                                <div class="md:col-span-2">
                                    <label for="password" class="block text-sm font-semibold text-gray-700">
                                        Contraseña <span class="text-red-600">*</span>
                                    </label>
                                    <input type="password" name="password" id="password"
                                           placeholder="Mínimo 8 caracteres, incluir mayúsculas, minúsculas y números"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:border-[#9F2241] focus:ring-[#9F2241]" />

                                    <p class="text-gray-500 text-sm mt-2 flex items-start gap-2">
                                        <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                                        <span>
                                            La contraseña debe tener al menos 8 caracteres e incluir letras mayúsculas, minúsculas y números.
                                        </span>
                                    </p>

                                    @error('password')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Institución + Subdependencia --}}
                        @php
                            // Mapa: institucion_id => [ {id, nombre}, ... ]
                            $subdepsByInst = $subdependencias
                                ->groupBy('institucion_id')
                                ->map(fn($items) => $items->map(fn($s) => ['id' => $s->id, 'nombre' => $s->nombre])->values())
                                ->toArray();
                        @endphp

                        <div
                            x-data="{
                                institucionId: '{{ old('institucion_id') }}',
                                subdependenciaId: '{{ old('subdependencia_id') }}',
                                subdepsByInst: @js($subdepsByInst),
                                get subdeps() {
                                    return this.subdepsByInst[this.institucionId] ?? [];
                                }
                            }"
                            class="space-y-4"
                        >
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/15">
                                    <i class="fas fa-building"></i>
                                </span>
                                <h4 class="font-semibold text-gray-800">Asignación institucional</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Institución --}}
                                <div>
                                    <label for="institucion_id" class="block text-sm font-semibold text-gray-700">
                                        Institución <span class="text-red-600">*</span>
                                    </label>

                                    <select
                                        name="institucion_id"
                                        id="institucion_id"
                                        required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                               focus:border-[#9F2241] focus:ring-[#9F2241]"
                                        x-model="institucionId"
                                        @change="subdependenciaId = ''"
                                    >
                                        <option value="" disabled selected>Seleccione una institución</option>

                                        @foreach($instituciones as $inst)
                                            <option value="{{ $inst->id }}">
                                                {{ $inst->siglas ? ($inst->siglas.' - '.$inst->nombre) : $inst->nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('institucion_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Subdependencia --}}
                                <div>
                                    <label for="subdependencia_id" class="block text-sm font-semibold text-gray-700">
                                        Subdependencia (opcional)
                                    </label>

                                    <select
                                        name="subdependencia_id"
                                        id="subdependencia_id"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                               focus:border-[#9F2241] focus:ring-[#9F2241] disabled:bg-gray-100"
                                        x-model="subdependenciaId"
                                        :disabled="!institucionId || subdeps.length === 0"
                                    >
                                        <option value="">Sin subdependencia</option>

                                        <template x-for="s in subdeps" :key="s.id">
                                            <option :value="s.id" x-text="s.nombre"></option>
                                        </template>
                                    </select>

                                    <p class="text-gray-500 text-sm mt-2 flex items-start gap-2">
                                        <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                                        <span>Selecciona primero una institución para ver sus subdependencias.</span>
                                    </p>

                                    @error('subdependencia_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-left"></i>
                                Regresar a Lista de Usuarios
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#9F2241] text-white font-semibold shadow-sm hover:bg-[#691C32] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9F2241] transition">
                                <i class="fas fa-check"></i>
                                Crear Usuario
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>
