{{-- resources/views/admin/users/create.blade.php --}}
<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700">
                            Nombre completo
                        </label>
                        <input type="text" name="name" id="name"
                            value="{{ old('name') }}"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>

                        @error('name')
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
                        <label for="password" class="block font-medium text-sm text-gray-700">
                            Contraseña
                        </label>
                        <input type="password" name="password" id="password"
                            class="mt-1 block w-full rounded-lg border-gray-300"
                            required>

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

                    {{-- Botón --}}
                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                            Crear Usuario
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
