<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-[#9F2241]">Eliminar cuenta</h2>
        <p class="mt-1 text-sm text-gray-600">
            Una vez que elimines tu cuenta no podrás recuperarla. Esta acción es irreversible.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700"
    >
        {{ __('Eliminar cuenta') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-gray-900">
                ¿Estás seguro de que quieres eliminar tu cuenta?
            </h2>

            <p class="mt-2 text-sm text-gray-600">
                Para confirmar esta acción, ingresa tu contraseña.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="text-[#691C32]" />
                <x-text-input id="password" name="password" type="password"
                    class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241]"
                    autocomplete="current-password" />

                <x-input-error :messages="$errors->get('password')" class="mt-2 text-[#9F2241]" />
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-700">
                    {{ __('Eliminar cuenta') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
