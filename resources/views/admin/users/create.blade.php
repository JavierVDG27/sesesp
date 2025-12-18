{{-- resources/views/admin/users/create.blade.php --}}

<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tigh">
            Crear nuevo usuario
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- Mensaje de éxito --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf

                    {{-- Nombres --}}
                    <div class="mb-4">
                        <label for="nombres" class="block font-medium text-sm text-gray-700">Nombres</label>
                        <input type="text" name="nombres" id="nombres"
                            value="{{ old('nombres') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>
                        @error('nombres')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellido paterno --}}
                    <div class="mb-4">
                        <label for="apellido_paterno" class="block font-medium text-sm text-gray-700">Apellido paterno</label>
                        <input type="text" name="apellido_paterno" id="apellido_paterno"
                            value="{{ old('apellido_paterno') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>
                        @error('apellido_paterno')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Apellido materno --}}
                    <div class="mb-4">
                        <label for="apellido_materno" class="block font-medium text-sm text-gray-700">Apellido materno</label>
                        <input type="text" name="apellido_materno" id="apellido_materno"
                            value="{{ old('apellido_materno') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300">
                        @error('apellido_materno')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label for="email" class="block font-medium text-sm text-gray-700">
                            Correo electrónico
                        </label>
                        <input type="email" name="email" id="email"
                            value="{{ old('email') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>

                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-4">
                        <label for="password" class="block font-medium text-sm text-gray-700">Contraseña</label>
                        <input type="password" name="password" id="password"
                            placeholder="Mínimo 8 caracteres, incluir mayúsculas, minúsculas y números"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>

                        <p class="text-gray-500 text-sm mt-1">
                            La contraseña debe tener al menos 8 caracteres e incluir letras mayúsculas, minúsculas y números.
                        </p>

                        @error('password')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rol --}}
                    <div class="mb-6">
                        <label for="role_id" class="block font-medium text-sm text-gray-700">
                            Rol de usuario
                        </label>

                        <select name="role_id" id="role_id"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>
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
                        class="space-y-6"
                    >
                        {{-- Institución --}}
                        <div>
                            <label for="institucion_id" class="block font-medium text-sm text-gray-700">
                                Institución
                            </label>

                            <select
                                name="institucion_id"
                                id="institucion_id"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                                required
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
                            <label for="subdependencia_id" class="block font-medium text-sm text-gray-700">
                                Subdependencia (opcional)
                            </label>

                            <select
                                name="subdependencia_id"
                                id="subdependencia_id"
                                class="mt-1 block w-full rounded-lg border-gray-300"
                                x-model="subdependenciaId"
                                :disabled="!institucionId || subdeps.length === 0"
                            >
                                <option value="">Sin subdependencia</option>

                                <template x-for="s in subdeps" :key="s.id">
                                    <option :value="s.id" x-text="s.nombre"></option>
                                </template>
                            </select>

                            <p class="text-gray-500 text-sm mt-1">
                                Selecciona primero una institución para ver sus subdependencias.
                            </p>

                            @error('subdependencia_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Botón --}}
                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                            Crear Usuario
                        </button>
                    </div>

                </form>
                <a href="{{ route('admin.users.index') }}"
                class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    ← Regresar a Lista de Usuarios
                </a>

            </div>
        </div>
    </div>

</x-app-layout>
