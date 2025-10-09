<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-white">Eliminar cuenta</h2>
        <p class="mt-1 text-sm text-gray-100">
            Una vez que elimines tu cuenta, todos los datos serán eliminados permanentemente. 
            Descarga la información que necesites antes de continuar.
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-white text-[#9F2241] hover:bg-gray-200 font-semibold px-4 py-2 rounded-xl">
        {{ __('Eliminar cuenta') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-medium text-[#9F2241]">¿Estás seguro de eliminar tu cuenta?</h2>
            <p class="mt-1 text-sm text-gray-600">
                Esta acción no se puede deshacer. Ingresa tu contraseña para confirmar.
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="text-[#691C32]" />
                <x-text-input id="password" name="password" type="password"
                    class="mt-1 block w-full rounded-xl border-[#9F2241] focus:border-[#691C32] focus:ring-[#9F2241] bg-white text-gray-800"
                    placeholder="********" />
                <x-input-error class="mt-2 text-[#9F2241]" :messages="$errors->get('password')" />
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button
                        type="submit"
                        class="text-white bg-[#9F2241] hover:bg-[#691C32] font-semibold px-4 py-2 rounded-xl transition">
                        {{ __('Eliminar cuenta') }}
                    </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
