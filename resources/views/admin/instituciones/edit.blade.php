<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Editar Institución
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">
                <h1 class="text-2xl font-bold text-[#691C32] mb-1">Actualizar institución</h1>
                <p class="text-gray-600 text-sm mb-6">Modifica los datos y guarda cambios.</p>

                <form method="POST" action="{{ route('admin.instituciones.update', $institucion) }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nombre</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $institucion->nombre) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-[#9F2241] focus:ring-[#9F2241]"
                               required>
                        @error('nombre') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Siglas (opcional)</label>
                        <input type="text" name="siglas" value="{{ old('siglas', $institucion->siglas) }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-[#9F2241] focus:ring-[#9F2241]"
                               placeholder="Ej. SESESP">
                        @error('siglas') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    @php
                        // Bag único para esta pantalla (solo 1 institución)
                        $bag = 'edit_institucion_'.$institucion->id;

                        $ordenValue = $errors->hasBag($bag)
                            ? old('orden', $institucion->orden ?? 0)
                            : ($institucion->orden ?? 0);
                    @endphp

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Orden</label>
                        <input type="number" name="orden"
                            value="{{ $ordenValue }}"
                            min="0" max="9999"
                            class="mt-1 block w-full rounded-xl border-gray-300 focus:border-[#9F2241] focus:ring-[#9F2241]">
                        <p class="text-xs text-gray-500 mt-1">Menor número = aparece primero.</p>

                        @error('orden', $bag)
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('admin.instituciones.index') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
