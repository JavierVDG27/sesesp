<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Subdependencias
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
                        <h1 class="text-2xl text-center font-bold text-[#691C32]">Gestión de Subdependencias</h1>
                        <p class="text-gray-600 text-sm">Selecciona una institución, reordena con ↑↓ y guarda al final.</p>
                    </div>

                    <a href="{{ route('admin.subdependencias.create') }}"
                       class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                        + Nueva subdependencia
                    </a>
                </div>

                {{-- FILTRO POR INSTITUCIÓN --}}
                <form method="GET" class="flex items-center gap-3 mb-5">
                    <label class="text-sm font-semibold text-gray-700">Institución:</label>

                    <select name="institucion_id"
                            class="rounded-lg border-gray-300"
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
                           class="px-3 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Quitar filtro
                        </a>
                    @endif
                </form>

                {{-- SI NO HAY INSTITUCIÓN SELECCIONADA --}}
                @if(!$institucionId)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl">
                        Selecciona una institución para reordenar sus subdependencias.
                    </div>

                    <div class="mt-6 overflow-x-auto bg-white rounded-xl border border-gray-200">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-[#9F2241]/10 text-[#691C32] font-semibold">
                                    <th class="py-3 px-4">Nombre</th>
                                    <th class="py-3 px-4">Institución</th>
                                    <th class="py-3 px-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subdependencias as $sub)
                                    <tr class="border-t">
                                        <td class="py-3 px-4 font-semibold text-gray-800">{{ $sub->nombre }}</td>
                                        <td class="py-3 px-4 text-gray-700">{{ $sub->institucion?->nombre ?? '—' }}</td>
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-3">
                                                <a href="{{ route('admin.subdependencias.edit', $sub) }}"
                                                   class="text-[#9F2241] hover:underline font-semibold">
                                                    Editar
                                                </a>

                                                <button type="button"
                                                        class="text-red-600 hover:underline font-semibold"
                                                        onclick="if(confirm('¿Seguro que deseas eliminar esta subdependencia?')) document.getElementById('del-sub-{{ $sub->id }}').submit();">
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-8 px-4 text-center text-gray-500">
                                            No hay subdependencias registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($subdependencias, 'links'))
                        <div class="mt-6">
                            {{ $subdependencias->links() }}
                        </div>
                    @endif

                @else
                    {{-- REORDEN (BATCH) SOLO CUANDO HAY INSTITUCIÓN --}}
                    <form method="POST" action="{{ route('admin.subdependencias.orden.batch') }}" id="subOrdenForm">
                        @csrf
                        <input type="hidden" name="institucion_id" value="{{ $institucionId }}">
                        <div id="subHidden"></div>

                        <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                            <table class="w-full text-left">
                                <thead>
                                    <tr class="bg-[#9F2241]/10 text-[#691C32] font-semibold">
                                        <th class="py-3 px-4 w-44">Reordenar</th>
                                        <th class="py-3 px-4">Nombre</th>
                                        <th class="py-3 px-4">Institución</th>
                                        <th class="py-3 px-4">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody id="subTbody">
                                    @forelse($subdependencias as $sub)
                                        <tr class="border-t" data-id="{{ $sub->id }}">
                                            <td class="py-3 px-4">
                                                <div class="flex items-center gap-2">
                                                    <button type="button"
                                                            class="sub-up px-2 py-1 rounded border border-gray-300 hover:bg-gray-50"
                                                            title="Subir">↑</button>

                                                    <button type="button"
                                                            class="sub-down px-2 py-1 rounded border border-gray-300 hover:bg-gray-50"
                                                            title="Bajar">↓</button>

                                                    <span class="text-xs text-gray-500 sub-badge">#</span>
                                                </div>
                                            </td>

                                            <td class="py-3 px-4 font-semibold text-gray-800">
                                                {{ $sub->nombre }}
                                            </td>

                                            <td class="py-3 px-4 text-gray-700">
                                                {{ $sub->institucion?->nombre ?? '—' }}
                                            </td>

                                            <td class="py-3 px-4">
                                                <div class="flex items-center gap-3">
                                                    <a href="{{ route('admin.subdependencias.edit', $sub) }}"
                                                       class="text-[#9F2241] hover:underline font-semibold">
                                                        Editar
                                                    </a>

                                                    <button type="button"
                                                            class="text-red-600 hover:underline font-semibold"
                                                            onclick="if(confirm('¿Seguro que deseas eliminar esta subdependencia?')) document.getElementById('del-sub-{{ $sub->id }}').submit();">
                                                        Eliminar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-8 px-4 text-center text-gray-500">
                                                No hay subdependencias registradas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <p class="text-sm text-gray-500">
                                Ordena con ↑↓ (no recarga). Al final presiona “Guardar orden”.
                            </p>

                            <button type="submit"
                                    class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                                Guardar orden
                            </button>
                        </div>
                    </form>
                @endif

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