{{-- resources/views/admin/users/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Editar Usuario
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-xl rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b bg-[#691C32]">
                    <h3 class="text-lg font-semibold text-white">
                        Actualizar información del usuario
                    </h3>
                    <p class="text-sm text-gray-200">
                        Modifica los datos y guarda cambios.
                    </p>
                </div>

                <div class="p-6">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Nombres --}}
                        <div>
                            <label for="nombres" class="block text-sm font-medium text-gray-700">Nombre(s)</label>
                            <input type="text" id="nombres" name="nombres"
                                   value="{{ old('nombres', $user->nombres) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                   required>
                            @error('nombres')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Apellido paterno --}}
                        <div>
                            <label for="apellido_paterno" class="block text-sm font-medium text-gray-700">Apellido paterno</label>
                            <input type="text" id="apellido_paterno" name="apellido_paterno"
                                   value="{{ old('apellido_paterno', $user->apellido_paterno) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                   required>
                            @error('apellido_paterno')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Apellido materno --}}
                        <div>
                            <label for="apellido_materno" class="block text-sm font-medium text-gray-700">Apellido materno (opcional)</label>
                            <input type="text" id="apellido_materno" name="apellido_materno"
                                   value="{{ old('apellido_materno', $user->apellido_materno) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]">
                            @error('apellido_materno')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                            <input type="email" id="email" name="email"
                                   value="{{ old('email', $user->email) }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                   required>
                            @error('email')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Rol --}}
                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700">Rol</label>
                            <select id="role_id" name="role_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                    required>
                                <option value="" disabled>Seleccione un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ (int) old('role_id', $user->role_id) === (int) $role->id ? 'selected' : '' }}>
                                        {{ ucfirst($role->nombre ?? $role->name ?? '') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Institución --}}
                        <div>
                            <label for="institucion_id" class="block text-sm font-medium text-gray-700">Institución</label>
                            <select id="institucion_id" name="institucion_id"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                    required>
                                <option value="" disabled>Seleccione una institución</option>
                                @foreach($instituciones as $inst)
                                    <option value="{{ $inst->id }}"
                                        {{ (int) old('institucion_id', $user->institucion_id) === (int) $inst->id ? 'selected' : '' }}>
                                        {{ $inst->siglas ? ($inst->siglas.' - '.$inst->nombre) : $inst->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('institucion_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Password (opcional) --}}
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                Nueva contraseña (opcional)
                            </label>
                            <input type="password" id="password" name="password"
                                   class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                   placeholder="Dejar vacío para no cambiar">
                            <p class="text-xs text-gray-500 mt-2">
                                Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial.
                            </p>
                            @error('password')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Botones --}}
                        <div class="flex items-center justify-end gap-3 pt-4">
                            <a href="{{ route('admin.users.index') }}"
                               class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>

                            <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                                Guardar cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
