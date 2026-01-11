<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Editar Subdependencia
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Actualiza su institución o nombre.
                </p>
            </div>

            <a href="{{ route('admin.subdependencias.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white/15 transition">
                <i class="fas fa-arrow-left"></i>
                Regresar
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
                {{-- Header card --}}
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                            <i class="fas fa-pen-to-square"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-lg font-semibold text-gray-800">Editar subdependencia</h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Modifica los datos y guarda los cambios.
                            </p>
                        </div>
                    </div>

                    {{-- Mini resumen --}}
                    <div class="mt-4 rounded-2xl bg-gray-50 border border-gray-200 p-4">
                        <p class="text-xs text-gray-500 uppercase tracking-wider">Subdependencia seleccionada</p>
                        <p class="font-semibold text-gray-800 mt-1 break-words">{{ $subdependencia->nombre }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            Institución: <span class="font-semibold">{{ $subdependencia->institucion?->nombre ?? '—' }}</span>
                        </p>
                    </div>
                </div>

                <div class="p-6">
                    <form method="POST" action="{{ route('admin.subdependencias.update', $subdependencia) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        {{-- Institución --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                Institución <span class="text-red-600">*</span>
                            </label>

                            <select name="institucion_id"
                                    class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                           focus:border-[#9F2241] focus:ring-[#9F2241]"
                                    required>
                                @foreach($instituciones as $inst)
                                    <option value="{{ $inst->id }}"
                                        {{ old('institucion_id', $subdependencia->institucion_id) == $inst->id ? 'selected' : '' }}>
                                        {{ $inst->nombre }}{{ $inst->siglas ? ' ('.$inst->siglas.')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @error('institucion_id')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Nombre --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                Nombre <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="nombre" value="{{ old('nombre', $subdependencia->nombre) }}"
                                   class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                          focus:border-[#9F2241] focus:ring-[#9F2241]"
                                   required>
                            @error('nombre')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Acciones --}}
                        <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                            <a href="{{ route('admin.subdependencias.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-left"></i>
                                Volver
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#9F2241] text-white font-semibold shadow-sm hover:bg-[#691C32]
                                           focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9F2241] transition">
                                <i class="fas fa-save"></i>
                                Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
