<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-[#fff] leading-tight">
            {{ __('Perfil de Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Información del perfil --}}
            <div class="bg-white p-8 shadow-md rounded-2xl border-l-4 border-[#9F2241]">
                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Actualizar contraseña --}}
            <div class="bg-white p-8 shadow-md rounded-2xl border-l-4 border-[#691C32]">
                @include('profile.partials.update-password-form')
            </div>

            {{-- Eliminar cuenta --}}
            <div class="bg-[#9F2241] p-8 shadow-md rounded-2xl text-white">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
