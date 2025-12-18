<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Nueva Subdependencia
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-lg rounded-2xl p-6">
                <h1 class="text-2xl font-bold text-[#691C32] mb-1">Registrar subdependencia</h1>
                <p class="text-gray-600 text-sm mb-6">Asigna la subdependencia a una institución.</p>

                <form method="POST" action="{{ route('admin.subdependencias.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Institución</label>
                        <select name="institucion_id"
                                class="mt-1 block w-full rounded-xl border-gray-300 focus:border-[#9F2241] focus:ring-[#9F2241]"
                                required>
                            <option value="" disabled {{ old('institucion_id') ? '' : 'selected' }}>
                                Seleccione una institución
                            </option>
                            @foreach($instituciones as $inst)
                                <option value="{{ $inst->id }}" {{ old('institucion_id') == $inst->id ? 'selected' : '' }}>
                                    {{ $inst->nombre }}{{ $inst->siglas ? ' ('.$inst->siglas.')' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('institucion_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700">Nombre de subdependencia</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}"
                               class="mt-1 block w-full rounded-xl border-gray-300 focus:border-[#9F2241] focus:ring-[#9F2241]"
                               required>
                        @error('nombre')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <a href="{{ route('admin.subdependencias.index') }}"
                           class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-100 transition">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                            Guardar
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
