<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tigh">
            {{ __('Perfil de usuario') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Información del usuario --}}
            @include('profile.partials.update-profile-information-form')

            {{-- Actualizar contraseña --}}
            @include('profile.partials.update-password-form')

            {{-- Eliminar cuenta --}}
            @include('profile.partials.delete-user-form')

        </div>
    </div>
</x-app-layout>
