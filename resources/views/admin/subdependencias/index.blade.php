<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Subdependencias
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Selecciona una institución, reordena con ↑↓ y guarda al final.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dependencias.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white/15 transition">
                    <i class="fas fa-arrow-left"></i>
                    Regresar
                </a>

                <a href="{{ route('admin.subdependencias.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-[#9F2241] font-semibold shadow-sm hover:bg-gray-50 transition">
                    <i class="fas fa-plus"></i>
                    Nueva subdependencia
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-green-700">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="text-green-800 text-sm">
                            <p class="font-semibold">Operación realizada</p>
                            <p class="mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-red-700">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="text-red-800 text-sm">
                            <p class="font-semibold">No se pudo completar</p>
                            <p class="mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card principal --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                                <i class="fas fa-sitemap"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-semibold text-gray-800">Gestión de Subdependencias</h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    Filtra por institución para reordenar. Sin filtro, solo se muestra el listado general.
                                </p>
                            </div>
                        </div>

                        {{-- FILTRO POR INSTITUCIÓN --}}
                        <form method="GET" class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <label class="text-sm font-semibold text-gray-700">Institución:</label>

                            <select name="institucion_id"
                                    class="w-full sm:w-80 rounded-xl border-gray-300 shadow-sm
                                           focus:border-[#9F2241] focus:ring-[#9F2241]"
                                    onchange="this.form.submit()">
                                <option value="">— Selecciona —</option>
                                @foreach($instituciones as $inst)
                                    <option value="{{ $inst->id }}" {{ (int)$institucionId === (int)$inst->id ? 'selected' : '' }}>
                                        {{ $inst->nombre }}{{ $inst->siglas ? ' ('.$inst->siglas.')' : '' }}
                                    </option>
                                @endforeach
                            </select>

                            @if($institucionId)
                                <a href="{{ route('admin.subdependencias.index') }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                    <i class="fas fa-filter-circle-xmark text-gray-500"></i>
                                    Quitar filtro
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <div class="p-6 space-y-5">

                    {{-- SI NO HAY INSTITUCIÓN SELECCIONADA --}}
                    @if(!$institucionId)
                        <div class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3">
                            <div class="flex items-start gap-3">
                                <div class="mt-0.5 text-amber-700">
                                    <i class="fas fa-circle-info"></i>
                                </div>
                                <div class="text-amber-800 text-sm">
                                    <p class="font-semibold">Selecciona una institución</p>
                                    <p class="mt-0.5">Para reordenar subdependencias, primero aplica un filtro de institución.</p>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto bg-white rounded-2xl border border-gray-200">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50">
                                    <tr class="text-gray-700 font-semibold">
                                        <th class="py-3 px-4">Nombre</th>
                                        <th class="py-3 px-4">Institución</th>
                                        <th class="py-3 px-4">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($subdependencias as $sub)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="py-3 px-4">
                                                <div class="font-semibold text-gray-800 break-words">
                                                    {{ $sub->nombre }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-4 text-gray-700 break-words">
                                                {{ $sub->institucion?->nombre ?? '—' }}
                                            </td>
                                            <td class="py-3 px-4">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <a href="{{ route('admin.subdependencias.edit', $sub) }}"
                                                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                                                        <i class="fas fa-pen-to-square text-[#9F2241]"></i>
                                                        Editar
                                                    </a>

                                                    <button type="button"
                                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 transition"
                                                            onclick="if(confirm('¿Seguro que deseas eliminar esta subdependencia?')) document.getElementById('del-sub-{{ $sub->id }}').submit();">
                                                        <i class="fas fa-trash"></i>
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-10 px-4 text-center text-gray-500">
                                                No hay subdependencias registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($subdependencias, 'links'))
                            <div class="pt-2">
                                {{ $subdependencias->links() }}
                            </div>
                        @endif

                    @else
                        {{-- REORDEN (BATCH) SOLO CUANDO HAY INSTITUCIÓN --}}
                        <form method="POST" action="{{ route('admin.subdependencias.orden.batch') }}" id="subOrdenForm" class="space-y-4">
                            @csrf
                            <input type="hidden" name="institucion_id" value="{{ $institucionId }}">
                            <div id="subHidden"></div>

                            <div class="rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 text-[#9F2241]">
                                            <i class="fas fa-sort"></i>
                                        </div>
                                        <div class="text-sm text-gray-700">
                                            <p class="font-semibold">Modo reordenamiento activo</p>
                                            <p class="text-gray-600 mt-0.5">Ordena con ↑↓ (no recarga). Al final presiona “Guardar orden”.</p>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center gap-2 text-xs font-semibold bg-white border border-gray-200 px-3 py-1 rounded-full">
                                        <i class="fas fa-filter text-gray-500"></i>
                                        Institución seleccionada: {{ $instituciones->firstWhere('id', $institucionId)?->nombre ?? '—' }}
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto bg-white rounded-2xl border border-gray-200">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50">
                                        <tr class="text-gray-700 font-semibold">
                                            <th class="py-3 px-4 w-48">Orden</th>
                                            <th class="py-3 px-4">Nombre</th>
                                            <th class="py-3 px-4">Institución</th>
                                            <th class="py-3 px-4">Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody id="subTbody" class="divide-y divide-gray-100">
                                        @forelse($subdependencias as $sub)
                                            <tr class="hover:bg-gray-50 transition" data-id="{{ $sub->id }}">
                                                <td class="py-3 px-4">
                                                    <div class="flex items-center gap-2">
                                                        <button type="button"
                                                                class="sub-up inline-flex items-center justify-center h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 shadow-sm transition"
                                                                title="Subir"
                                                                aria-label="Subir">
                                                            <i class="fas fa-arrow-up text-gray-700"></i>
                                                        </button>

                                                        <button type="button"
                                                                class="sub-down inline-flex items-center justify-center h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 shadow-sm transition"
                                                                title="Bajar"
                                                                aria-label="Bajar">
                                                            <i class="fas fa-arrow-down text-gray-700"></i>
                                                        </button>

                                                        <span class="sub-badge inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/10">
                                                            <i class="fas fa-hashtag text-[11px]"></i>
                                                            #
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="py-3 px-4">
                                                    <div class="font-semibold text-gray-800 break-words">
                                                        {{ $sub->nombre }}
                                                    </div>
                                                </td>

                                                <td class="py-3 px-4 text-gray-700 break-words">
                                                    {{ $sub->institucion?->nombre ?? '—' }}
                                                </td>

                                                <td class="py-3 px-4">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <a href="{{ route('admin.subdependencias.edit', $sub) }}"
                                                           class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                                                            <i class="fas fa-pen-to-square text-[#9F2241]"></i>
                                                            Editar
                                                        </a>

                                                        <button type="button"
                                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 transition"
                                                                onclick="if(confirm('¿Seguro que deseas eliminar esta subdependencia?')) document.getElementById('del-sub-{{ $sub->id }}').submit();">
                                                            <i class="fas fa-trash"></i>
                                                            Eliminar
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-10 px-4 text-center text-gray-500">
                                                    No hay subdependencias registradas.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="pt-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <p class="text-sm text-gray-500 flex items-start gap-2">
                                    <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                                    <span>El orden se guarda cuando presionas “Guardar orden”.</span>
                                </p>

                                <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#9F2241] text-white font-semibold shadow-sm hover:bg-[#691C32] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9F2241] transition">
                                    <i class="fas fa-save"></i>
                                    Guardar orden
                                </button>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- FORMS OCULTOS PARA ELIMINAR (FUERA de cualquier form para evitar anidar) --}}
    @foreach($subdependencias as $sub)
        <form id="del-sub-{{ $sub->id }}"
              method="POST"
              action="{{ route('admin.subdependencias.destroy', $sub) }}"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tbody = document.getElementById('subTbody');
        const hidden = document.getElementById('subHidden');
        const form = document.getElementById('subOrdenForm');

        if (!tbody || !hidden || !form) return;

        function renumerar() {
            const rows = [...tbody.querySelectorAll('tr[data-id]')];
            rows.forEach((row, idx) => {
                const badge = row.querySelector('.sub-badge');
                if (badge) badge.textContent = `#${idx + 1}`;
            });
        }

        function rebuildHiddenItems() {
            hidden.innerHTML = '';
            const rows = [...tbody.querySelectorAll('tr[data-id]')];

            rows.forEach((row, idx) => {
                const id = row.dataset.id;
                const ord = idx + 1;

                const i = idx;

                const inId = document.createElement('input');
                inId.type = 'hidden';
                inId.name = `items[${i}][id]`;
                inId.value = id;

                const inOrd = document.createElement('input');
                inOrd.type = 'hidden';
                inOrd.name = `items[${i}][orden]`;
                inOrd.value = ord;

                hidden.appendChild(inId);
                hidden.appendChild(inOrd);
            });
        }

        function moveUp(row) {
            const prev = row.previousElementSibling;
            if (prev) tbody.insertBefore(row, prev);
        }

        function moveDown(row) {
            const next = row.nextElementSibling;
            if (next) tbody.insertBefore(next, row);
        }

        renumerar();

        tbody.addEventListener('click', (e) => {
            const up = e.target.closest('.sub-up');
            const down = e.target.closest('.sub-down');
            if (!up && !down) return;

            const row = e.target.closest('tr');
            if (!row) return;

            if (up) moveUp(row);
            if (down) moveDown(row);

            renumerar();
        });

        form.addEventListener('submit', () => {
            rebuildHiddenItems();
        });
    });
    </script>
</x-app-layout>