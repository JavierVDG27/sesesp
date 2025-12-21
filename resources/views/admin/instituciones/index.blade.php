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

            @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-800 border border-red-200 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('admin.dependencias.index') }}"
                       class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                        ← Regresar a Dependencias
                    </a>

                    <div>
                        <h1 class="text-2xl text-center font-bold text-[#691C32]">Gestión de Instituciones</h1>
                        <p class="text-gray-600 text-sm">Alta, edición y administración de instituciones.</p>
                    </div>

                    <a href="{{ route('admin.instituciones.create') }}"
                       class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                        + Nueva institución
                    </a>
                </div>

                {{-- FORM BATCH (NO anidar forms dentro del tbody) --}}
                <form method="POST" action="{{ route('admin.instituciones.orden.batch') }}" id="ordenForm">
                    @csrf

                    <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#9F2241]/10 text-[#691C32] font-semibold">
                                    <th class="py-3 px-4 w-40">Reordenar</th>
                                    <th class="py-3 px-4">Nombre</th>
                                    <th class="py-3 px-4">Siglas</th>
                                    <th class="py-3 px-4">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="instTbody">
                                @forelse($instituciones as $inst)
                                    <tr class="border-t" data-id="{{ $inst->id }}">
                                        {{-- REORDENAR (↑ ↓) + ids[] --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                        class="move-up px-2 py-1 rounded border border-gray-300 hover:bg-gray-50"
                                                        title="Subir">↑</button>

                                                <button type="button"
                                                        class="move-down px-2 py-1 rounded border border-gray-300 hover:bg-gray-50"
                                                        title="Bajar">↓</button>

                                                <span class="text-xs text-gray-500 order-badge">#{{ $inst->orden }}</span>
                                            </div>

                                            <input type="hidden" name="ids[]" value="{{ $inst->id }}">
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

                                                {{-- Eliminar SIN form aquí (para no anidar) --}}
                                                <button type="button"
                                                        class="text-red-600 hover:underline font-semibold"
                                                        onclick="if(confirm('¿Seguro que deseas eliminar esta institución?')) document.getElementById('del-inst-{{ $inst->id }}').submit();">
                                                    Eliminar
                                                </button>
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

                    <div class="mt-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            Usa ↑/↓ para acomodar y luego guarda el orden.
                        </p>

                        <button type="submit"
                                class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                            Guardar orden
                        </button>
                    </div>
                </form>

                {{-- Si sigues usando paginación, déjalo; pero ojo: solo ordenas la página actual --}}
                @if(method_exists($instituciones, 'links'))
                    <div class="mt-6">
                        {{ $instituciones->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- FORMS OCULTOS PARA ELIMINAR (FUERA del form batch) --}}
    @foreach($instituciones as $inst)
        <form id="del-inst-{{ $inst->id }}"
              method="POST"
              action="{{ route('admin.instituciones.destroy', $inst) }}"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <script>
    document.addEventListener('DOMContentLoaded', () => {
    const tbody = document.getElementById('instTbody');

    function renumerarBadges() {
        const rows = [...tbody.querySelectorAll('tr[data-id]')];
        rows.forEach((row, idx) => {
        const badge = row.querySelector('.order-badge');
        if (badge) badge.textContent = `#${idx + 1}`;
        });
    }
    renumerarBadges();

    tbody.addEventListener('click', (e) => {
        const up = e.target.closest('.move-up');
        const down = e.target.closest('.move-down');
        if (!up && !down) return;

        const row = e.target.closest('tr');
        if (!row) return;

        if (up) {
        const prev = row.previousElementSibling;
        if (prev) tbody.insertBefore(row, prev);
        }

        if (down) {
        const next = row.nextElementSibling;
        if (next) tbody.insertBefore(next, row);
        }
        renumerarBadges();
    });
    });
    </script>

</x-app-layout>
