<section>
    <header>
        <h2 class="text-lg font-medium text-[#9F2241]">Información del perfil - ADMINISTRADOR </h2>
        <p class="mt-1 text-sm text-gray-600">Actualiza la información de tu cuenta y correo electrónico.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nombres -->
        <div>
            <x-input-label for="nombres" value="Nombres" class="text-[#691C32]" />
            <x-text-input id="nombres" name="nombres" type="text"
                class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                :value="old('nombres', $user->nombres)" required autofocus autocomplete="given-name" />
            <x-input-error class="mt-2 text-[#9F2241]" :messages="$errors->get('nombres')" />
        </div>

        <!-- Apellido Paterno -->
        <div>
            <x-input-label for="apellido_paterno" value="Apellido paterno" class="text-[#691C32]" />
            <x-text-input id="apellido_paterno" name="apellido_paterno" type="text"
                class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                :value="old('apellido_paterno', $user->apellido_paterno)" required autocomplete="family-name" />
            <x-input-error class="mt-2 text-[#9F2241]" :messages="$errors->get('apellido_paterno')" />
        </div>

        <!-- Apellido Materno -->
        <div>
            <x-input-label for="apellido_materno" value="Apellido materno" class="text-[#691C32]" />
            <x-text-input id="apellido_materno" name="apellido_materno" type="text"
                class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                :value="old('apellido_materno', $user->apellido_materno)" autocomplete="additional-name" />
            <x-input-error class="mt-2 text-[#9F2241]" :messages="$errors->get('apellido_materno')" />
        </div>

        <!-- Correo electrónico -->
        <div>
            <x-input-label for="email" :value="__('Correo electrónico')" class="text-[#691C32]" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-[#9F2241]" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#9F2241] hover:bg-[#691C32] focus:ring-[#9F2241]">
                {{ __('Guardar cambios') }}
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-[#691C32]">Guardado correctamente.</p>
            @endif
        </div>

        <!-- Información del Rol -->
        <div>
            <x-input-label value="Rol" class="text-[#691C32]" />
            <input type="text" disabled
                class="mt-1 block w-full rounded-xl border-gray-300 bg-gray-100 text-gray-800"
                value="{{ ucfirst($user->role->name) }}">
        </div>

        <!-- Siglas de la institución -->
        <div>
            <x-input-label value="Institución" class="text-[#691C32]" />
            <input type="text" disabled
                class="mt-1 block w-full rounded-xl border-gray-300 bg-gray-100 text-gray-800"
                value="{{ $user->institucion?->siglas ?? 'Sin asignar' }}">
        </div>
    </form>
</section>
