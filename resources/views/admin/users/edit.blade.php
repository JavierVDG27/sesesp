<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Usuario
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-lg p-6">

                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Nombre --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                            class="w-full mt-1 rounded-lg border-gray-300">
                        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Correo</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                            class="w-full mt-1 rounded-lg border-gray-300">
                        @error('email') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    {{-- Rol --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Rol</label>
                        <select name="role_id" class="w-full mt-1 rounded-lg border-gray-300">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" 
                                    @if($user->role_id == $role->id) selected @endif>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Password --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium">Nueva contraseña (opcional)</label>
                        <input type="password" name="password"
                            class="w-full mt-1 rounded-lg border-gray-300">
                        <p class="text-gray-500 text-sm">Déjala vacía si no deseas cambiarla.</p>
                        @error('password') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Guardar Cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

</x-app-layout>
