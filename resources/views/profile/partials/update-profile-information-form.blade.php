<section>
    <header>
        <h2 class="text-lg font-medium text-[#9F2241]">Información del perfil</h2>
        <p class="mt-1 text-sm text-gray-600">Actualiza la información de tu cuenta y correo electrónico.</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="__('Nombre')" class="text-[#691C32]" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-[#9F2241]" :messages="$errors->get('name')" />
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
    </form>
</section>
