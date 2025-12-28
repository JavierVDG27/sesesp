<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Asignación FASP a capturistas
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-2xl p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">Asignaciones</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            El validador asigna <span class="font-semibold">Subprogramas</span> a capturistas por año.
                        </p>
                    </div>

                    <form method="GET" class="flex items-center gap-2">
                        <input type="number" name="year" value="{{ $year }}" class="border rounded-lg px-3 py-2 text-sm w-28">
                        <input type="text" name="entidad" value="{{ $entidad }}" class="border rounded-lg px-3 py-2 text-sm w-28">
                        <button class="px-3 py-2 bg-[#691C32] text-white text-sm rounded-lg hover:bg-[#4e1324]">
                            Filtrar
                        </button>
                    </form>
                </div>

                {{-- Formulario asignación --}}
                <form method="POST" action="{{ route('validador.fasp_asignaciones.store') }}" class="border rounded-2xl p-4 bg-gray-50">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <input type="hidden" name="entidad" value="{{ $entidad }}">

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Capturista</label>
                            <select name="user_id" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                <option value="">-- Selecciona --</option>
                                @foreach($capturistas as $c)
                                    <option value="{{ $c->id }}">
                                        {{ $c->nombres }} {{ $c->apellido_paterno }} ({{ $c->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Eje</label>
                            <select id="ejeSelect" name="eje" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                <option value="">-- Selecciona --</option>
                                @foreach($ejes as $eje)
                                    <option value="{{ $eje }}">{{ $eje }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Programa</label>
                            <select id="programaSelect" name="programa" class="w-full border rounded-lg px-3 py-2 text-sm" required disabled>
                                <option value="">-- Selecciona un eje --</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Modo</label>
                            <select id="modoSelect" name="modo" class="w-full border rounded-lg px-3 py-2 text-sm" required>
                                <option value="programa">Asignar TODO el programa</option>
                                <option value="subprograma">Seleccionar subprogramas</option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button class="w-full px-4 py-2 bg-[#691C32] text-white text-sm font-semibold rounded-lg hover:bg-[#4e1324]">
                                Asignar
                            </button>
                        </div>
                    </div>

                    <div id="subprogramasBox" class="mt-4 hidden">
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Subprogramas</label>
                        <div id="subprogramasList" class="grid grid-cols-1 md:grid-cols-4 gap-2"></div>
                        <p class="text-xs text-gray-500 mt-2">Selecciona uno o varios subprogramas.</p>
                    </div>
                </form>

                {{-- Tabla asignaciones existentes --}}
                <div class="mt-6 overflow-x-auto border border-gray-200 rounded-xl">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-4 py-3 border-b text-left">Año</th>
                                <th class="px-4 py-3 border-b text-left">Entidad</th>
                                <th class="px-4 py-3 border-b text-left">Eje</th>
                                <th class="px-4 py-3 border-b text-left">Programa</th>
                                <th class="px-4 py-3 border-b text-left">Subprograma</th>
                                <th class="px-4 py-3 border-b text-left">Capturista</th>
                                <th class="px-4 py-3 border-b text-left">Asignó</th>
                                <th class="px-4 py-3 border-b text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($asignaciones as $a)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ $a->year }}</td>
                                    <td class="px-4 py-3">{{ $a->entidad }}</td>
                                    <td class="px-4 py-3">{{ $a->eje }}</td>
                                    <td class="px-4 py-3">{{ $a->programa }}</td>
                                    <td class="px-4 py-3">{{ $a->subprograma }}</td>
                                    <td class="px-4 py-3">
                                        {{ $a->capturista->nombres ?? $a->capturista->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $a->asignador->nombres ?? $a->asignador->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <form method="POST" action="{{ route('validador.fasp_asignaciones.destroy', $a) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-2 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700">
                                                Quitar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        No hay asignaciones para el filtro actual.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    <script>
        const year = @json($year);
        const entidad = @json($entidad);
        const urlOpciones = @json(route('validador.fasp_asignaciones.opciones'));

        const ejeSelect = document.getElementById('ejeSelect');
        const programaSelect = document.getElementById('programaSelect');
        const modoSelect = document.getElementById('modoSelect');

        const subBox = document.getElementById('subprogramasBox');
        const subList = document.getElementById('subprogramasList');

        async function fetchJSON(url) {
            const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
            if (!res.ok) return {};
            return await res.json();
        }

        function resetProgramas() {
            programaSelect.innerHTML = `<option value="">-- Selecciona un eje --</option>`;
            programaSelect.disabled = true;
            resetSubprogramas();
        }

        function resetSubprogramas() {
            subList.innerHTML = '';
            subBox.classList.add('hidden');
        }

        ejeSelect.addEventListener('change', async () => {
            resetProgramas();
            const eje = ejeSelect.value;
            if (!eje) return;

            const data = await fetchJSON(`${urlOpciones}?year=${year}&entidad=${entidad}&eje=${encodeURIComponent(eje)}`);
            const programas = data.programas || [];

            programaSelect.disabled = false;
            programaSelect.innerHTML = `<option value="">-- Selecciona --</option>` + programas.map(p => (
                `<option value="${p}">${p}</option>`
            )).join('');
        });

        programaSelect.addEventListener('change', async () => {
            resetSubprogramas();
            const eje = ejeSelect.value;
            const programa = programaSelect.value;
            if (!eje || !programa) return;

            if (modoSelect.value === 'subprograma') {
                await cargarSubprogramas(eje, programa);
            }
        });

        modoSelect.addEventListener('change', async () => {
            resetSubprogramas();
            const eje = ejeSelect.value;
            const programa = programaSelect.value;
            if (!eje || !programa) return;

            if (modoSelect.value === 'subprograma') {
                await cargarSubprogramas(eje, programa);
            }
        });

        async function cargarSubprogramas(eje, programa) {
            const data = await fetchJSON(`${urlOpciones}?year=${year}&entidad=${entidad}&eje=${encodeURIComponent(eje)}&programa=${encodeURIComponent(programa)}`);
            const subs = data.subprogramas || [];
            if (!subs.length) return;

            subBox.classList.remove('hidden');
            subList.innerHTML = subs.map(s => `
                <label class="flex items-center gap-2 bg-white border rounded-lg px-3 py-2">
                    <input type="checkbox" name="subprogramas[]" value="${s}">
                    <span class="text-sm">${s}</span>
                </label>
            `).join('');
        }
    </script>
</x-app-layout>
