<section>
    <header>
        <h2 class="text-lg font-medium text-[#9F2241]">Actualizar contraseña</h2>
        <p class="mt-1 text-sm text-gray-600">
            Asegúrate de que tu nueva contraseña sea segura.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- Contraseña actual --}}
        <div>
            <x-input-label for="current_password" :value="__('Contraseña actual')" class="text-[#691C32]" />
            <x-text-input id="current_password" name="current_password" type="password"
                class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('current_password')" class="mt-2 text-[#9F2241]" />
        </div>

        {{-- Nueva contraseña --}}
        <div>
            <x-input-label for="password" :value="__('Nueva contraseña')" class="text-[#691C32]" />
            <x-text-input id="password" name="password" type="password"
                class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#9F2241]" />
        </div>

        {{-- Confirmación --}}
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmar contraseña')" class="text-[#691C32]" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-[#9F2241]" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button class="bg-[#9F2241] hover:bg-[#691C32]">
                {{ __('Guardar contraseña') }}
            </x-primary-button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-[#691C32]">Contraseña actualizada correctamente.</p>
            @endif
        </div>
    </form>
</section>
