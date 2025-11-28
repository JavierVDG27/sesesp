{{-- resources/views/admin/users/edit.blade.php --}}
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
                        <label class="block text-gray-700 font-medium">Nombre completo</label>
                        <input type="text" name="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full mt-1 rounded-lg border-gray-300">
                        @error('name')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Correo electrónico</label>
                        <input type="email" name="email"
                            value="{{ old('email', $user->email) }}"
                            class="w-full mt-1 rounded-lg border-gray-300">
                        @error('email')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Rol --}}
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium">Rol</label>
                        <select name="role_id" class="w-full mt-1 rounded-lg border-gray-300">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ $user->role_id == $role->id ? 'selected' : '' }}>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Institución --}}
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium">Institución</label>

                        <select name="institucion_id" class="w-full mt-1 rounded-lg border-gray-300">
                            <option value="" disabled>Seleccione una institución</option>

                            @foreach($instituciones as $inst)
                                <option value="{{ $inst->id }}"
                                    {{ $user->institucion_id == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->nombre }}
                                </option>
                            @endforeach
                        </select>

                        @error('institucion_id')
                            <p class="text-red-600 text-sm">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="mb-6">
                        <label class="blo
