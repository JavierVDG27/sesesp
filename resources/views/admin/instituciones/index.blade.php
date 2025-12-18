<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Instituciones
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 border border-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                            <a href="{{ route('admin.dependencias.index') }}"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                                ← Regresar a Dependencias
                            </a>
                    <div>
                        <h1 class="text-2xl font-bold text-[#691C32]">Gestión de Instituciones</h1>
                        <p class="text-gray-600 text-sm">Alta, edición y administración de instituciones.</p>
                    </div>

                    <a href="{{ route('admin.instituciones.create') }}"
                       class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                        + Nueva institución
                    </a>
                </div>

                <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#9F2241]/10 text-[#691C32] font-semibold">
                                <th class="py-3 px-4 w-40">Orden</th>
                                <th class="py-3 px-4">Nombre</th>
                                <th class="py-3 px-4">Siglas</th>
                                <th class="py-3 px-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($instituciones as $inst)
                                <tr class="border-t">
                                    {{-- ORDEN EDITABLE --}}
                                    <td class="py-3 px-4">
                                        @php
                                            $bag = 'orden_'.$inst->id;

                                            // Solo usa old() si esta fila fue la que falló (si existe el bag)
                                            $ordenValue = $errors->hasBag($bag)
                                                ? old('orden', $inst->orden ?? 0)
                                                : ($inst->orden ?? 0);
                                        @endphp

                                        <form method="POST"
                                            action="{{ route('admin.instituciones.orden', ['institucione' => $inst->id]) }}#inst-{{ $inst->id }}"
                                            class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <input
                                                type="number"
                                                name="orden"
                                                min="0"
                                                max="9999"
                                                value="{{ $ordenValue }}"
                                                class="w-24 rounded-lg border-gray-300 shadow-sm focus:ring-[#9F2241] focus:border-[#9F2241]"
                                            >

                                            <button type="submit"
                                                class="px-3 py-2 rounded-lg bg-[#9F2241] text-white text-sm font-semibold hover:bg-[#691C32] transition">
                                                Guardar
                                            </button>
                                        </form>

                                        @error('orden', $bag)
                                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </td>

                                    <td class="py-3 px-4 font-semibold text-gray-800">
                                        {{ $inst->nombre }}
                                    </td>

                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $inst->siglas ?? '—' }}
                                    </td>

                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.instituciones.edit', $inst) }}"
                                               class="text-[#9F2241] hover:underline font-semibold">
                                                Editar
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('admin.instituciones.destroy', $inst) }}"
                                                  onsubmit="return confirm('¿Seguro que deseas eliminar esta institución?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline font-semibold">
                                                    Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-8 px-4 text-center text-gray-500">
                                        No hay instituciones registradas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($instituciones, 'links'))
                    <div class="mt-6">
                        {{ $instituciones->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
