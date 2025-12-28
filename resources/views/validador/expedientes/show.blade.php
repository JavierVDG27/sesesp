{{-- resources/views/validador/expedientes/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Revisión de Expediente') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">

                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">
                            Expediente: {{ $expediente->folio }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Proyecto: <span class="font-semibold">{{ $expediente->nombre_proyecto }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            Capturista: {{ optional($expediente->usuario)->nombres ?? optional($expediente->usuario)->name ?? 'N/A' }}
                            · Año: {{ $expediente->anio_ejercicio }}
                            · Dependencia: {{ $expediente->dependencia }}
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('validador.expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                            ← Volver a bandeja
                        </a>
                    </div>
                </div>

                {{-- Badge estatus --}}
                <div class="mb-6">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        Estatus: En validación
                    </span>
                </div>

                {{-- Validación de asignación FASP (por institución del capturista) --}}
                @if(isset($coincideAsignacion) && !$coincideAsignacion)
                        <div class="mb-6 px-4 py-3 rounded-lg bg-red-100 text-red-800 border border-red-200">
                            <div class="font-semibold">⚠️ Advertencia: fuera de asignación</div>
                            <div class="text-sm mt-1">
                                {{ $motivoNoCoincide ?? 'El expediente no coincide con las asignaciones FASP de la institución del capturista.' }}
                            </div>
                            <div class="text-xs mt-2 text-red-700">
                                Recomendación: Rechazar y solicitar corrección / reasignación.
                            </div>
                        </div>
                    @else
                        <div class="mb-6 px-4 py-3 rounded-lg bg-green-100 text-green-800 border border-green-200">
                            <div class="font-semibold">✓ Asignación correcta</div>
                            <div class="text-sm mt-1">
                                La estructura FASP coincide con las asignaciones de la institución del capturista.
                            </div>
                        </div>
                @endif

                {{-- ===================== DATOS GENERALES ===================== --}}
                <div class="border border-gray-200 rounded-xl p-6 mb-6">
                    <h4 class="text-lg font-semibold text-[#691C32] mb-4">Datos generales</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 text-xs">Entidad</p>
                            <p class="font-medium text-gray-800">{{ $expediente->entidad ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Eje</p>
                            <p class="font-medium text-gray-800">{{ $expediente->eje ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Programa</p>
                            <p class="font-medium text-gray-800">{{ $expediente->programa ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs">Subprograma</p>
                            <p class="font-medium text-gray-800">{{ $expediente->subprograma ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-gray-500 text-xs">Tema</p>
                            <p class="font-medium text-gray-800">{{ $expediente->tema ?? '-' }}</p>
                        </div>
                        <div class="md:col-span-3">
                            <p class="text-gray-500 text-xs">Área ejecutora</p>
                            <p class="font-medium text-gray-800">{{ $expediente->area_ejecutora ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                {{-- ===================== PRESUPUESTO ===================== --}}
                <div class="border border-gray-200 rounded-xl p-6 mb-6">
                    <h4 class="text-lg font-semibold text-[#691C32] mb-4">Presupuesto</h4>

                    <div class="overflow-x-auto border border-gray-200 rounded-xl">
                        <table class="min-w-full text-xs">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 border-b min-w-[90px]">Capítulo</th>
                                    <th class="px-3 py-2 border-b min-w-[110px]">Partida</th>
                                    <th class="px-3 py-2 border-b min-w-[240px]">Concepto</th>
                                    <th class="px-3 py-2 border-b min-w-[180px]">Bien</th>
                                    <th class="px-3 py-2 border-b min-w-[120px]">Unidad</th>
                                    <th class="px-3 py-2 border-b min-w-[90px] text-right">Cantidad</th>
                                    <th class="px-3 py-2 border-b min-w-[130px]">Persona</th>
                                    <th class="px-3 py-2 border-b min-w-[80px]">RLC</th>
                                    <th class="px-3 py-2 border-b min-w-[120px] text-right">FASP Fed.</th>
                                    <th class="px-3 py-2 border-b min-w-[120px] text-right">FASP Mun.</th>
                                    <th class="px-3 py-2 border-b min-w-[120px] text-right">FASP Sub.</th>
                                    <th class="px-3 py-2 border-b min-w-[120px] text-right">Estatal</th>
                                    <th class="px-3 py-2 border-b min-w-[120px] text-right">Mun. Est.</th>
                                    <th class="px-3 py-2 border-b min-w-[120px] text-right">Est. Sub.</th>
                                    <th class="px-3 py-2 border-b min-w-[140px] text-right">Total</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @php $totalGeneral = 0; @endphp

                                @forelse($expediente->presupuestos as $p)
                                    @php $totalGeneral += (float)($p->total_financiamiento ?? 0); @endphp
                                    <tr>
                                        <td class="px-3 py-2">{{ $p->capitulo ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->partida ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->descripcion_concepto ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->bien ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->unidad ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right">{{ number_format((float)($p->cantidad ?? 0), 2) }}</td>
                                        <td class="px-3 py-2">{{ $p->persona ?? '-' }}</td>
                                        <td class="px-3 py-2">{{ $p->rlc ?? '-' }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format((float)($p->fasp_federal ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format((float)($p->fasp_municipal ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format((float)($p->fasp_subtotal ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format((float)($p->est_estatal ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format((float)($p->est_municipal ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-right">${{ number_format((float)($p->est_subtotal ?? 0), 2) }}</td>
                                        <td class="px-3 py-2 text-right font-semibold">${{ number_format((float)($p->total_financiamiento ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="15" class="px-3 py-6 text-center text-gray-500">
                                            No hay conceptos de presupuesto registrados.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>

                            <tfoot class="bg-gray-50">
                                <tr>
                                    <th colspan="14" class="px-3 py-3 text-right text-sm font-semibold border-t">
                                        Total general:
                                    </th>
                                    <th class="px-3 py-3 text-right text-sm font-bold border-t">
                                        ${{ number_format($totalGeneral, 2) }}
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- ===================== HISTORIAL ===================== --}}
                <div class="border border-gray-200 rounded-xl p-6 mb-6">
                    <h4 class="text-lg font-semibold text-[#691C32] mb-4">Historial de cambios</h4>

                    <div class="overflow-x-auto border border-gray-200 rounded-xl">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2 border-b text-left">Fecha</th>
                                    <th class="px-4 py-2 border-b text-left">Usuario</th>
                                    <th class="px-4 py-2 border-b text-left">Cambio</th>
                                    <th class="px-4 py-2 border-b text-left">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($expediente->historiales as $h)
                                    <tr>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ $h->created_at?->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ optional($h->usuario)->nombres ?? optional($h->usuario)->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            <span class="font-medium">{{ $h->estado_anterior ?? '-' }}</span>
                                            →
                                            <span class="font-medium">{{ $h->estado_nuevo ?? '-' }}</span>
                                        </td>
                                        <td class="px-4 py-2 text-gray-700">
                                            {{ $h->observaciones ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                            No hay historial registrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Panel: Asignaciones FASP de la institución del capturista --}}
                <div class="mb-6 bg-white border border-gray-200 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="text-lg font-semibold text-[#691C32]">Asignaciones FASP (institución del capturista)</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                Comparación rápida entre el expediente y lo asignado para el año/entidad.
                            </p>
                        </div>

                        <a href="{{ route('validador.fasp_asignaciones_institucion.index', ['year' => $expediente->anio_ejercicio, 'entidad' => $expediente->entidad ?? '8300']) }}"
                        class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-800 text-xs font-semibold rounded-md hover:bg-gray-200 transition">
                            Ir a asignaciones →
                        </a>
                    </div>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="text-xs text-gray-500">Institución</div>
                            <div class="font-semibold text-gray-800">
                                {{ $institucionNombre ?? 'N/A (sin relación institucion en User)' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="text-xs text-gray-500">Año / Entidad</div>
                            <div class="font-semibold text-gray-800">
                                {{ $expediente->anio_ejercicio }} / {{ $expediente->entidad ?? '8300' }}
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-3">
                            <div class="text-xs text-gray-500">Eje / Programa / Subprograma</div>
                            <div class="font-semibold text-gray-800">
                                {{ $expediente->eje }} / {{ $expediente->programa }} / {{ $expediente->subprograma }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="text-xs font-semibold text-gray-600 mb-2">
                            Subprogramas asignados para:
                            <span class="text-gray-800">{{ $expediente->eje }} / {{ $expediente->programa }}</span>
                        </div>

                        @if(isset($subprogramasAsignados) && $subprogramasAsignados->count())
                            <div class="flex flex-wrap gap-2">
                                @foreach($subprogramasAsignados as $sub)
                                    @php $isActual = (string)$sub === (string)$expediente->subprograma; @endphp

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                        {{ $isActual ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $sub }}
                                        @if($isActual) <span class="ml-1">★</span> @endif
                                    </span>
                                @endforeach
                            </div>

                            @if(!in_array((string)$expediente->subprograma, $subprogramasAsignados->map(fn($x)=>(string)$x)->all(), true))
                                <div class="mt-3 px-4 py-3 rounded-lg bg-red-50 text-red-700 border border-red-100 text-sm">
                                    ⚠️ El subprograma del expediente <b>{{ $expediente->subprograma }}</b> no aparece dentro de los asignados para este eje/programa.
                                </div>
                            @endif
                        @else
                            <div class="px-4 py-3 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-100 text-sm">
                                No se encontraron subprogramas asignados para este Eje/Programa (o la institución no tiene asignaciones para ese año/entidad).
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Resumen completo de asignaciones de la institución --}}
                <div class="mb-6 bg-white border border-gray-200 rounded-2xl p-5">
                    <h4 class="text-lg font-semibold text-[#691C32]">Todo lo asignado a la institución</h4>
                    <p class="text-sm text-gray-600 mt-1">
                        Eje/Programa con sus subprogramas asignados para {{ $expediente->anio_ejercicio }} / {{ $expediente->entidad ?? '8300' }}.
                    </p>

                    @if(isset($resumenAsignaciones) && $resumenAsignaciones->count())
                        <div class="mt-4 space-y-3">
                            @foreach($resumenAsignaciones as $row)
                                @php
                                    $esMismo = ((string)$row['eje'] === (string)$expediente->eje) && ((string)$row['programa'] === (string)$expediente->programa);
                                @endphp

                                <div class="border rounded-xl p-4 {{ $esMismo ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="font-semibold text-gray-800">
                                            Eje {{ $row['eje'] }} · Programa {{ $row['programa'] }}
                                            @if($esMismo)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800">
                                                    Coincide con el expediente
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $row['subprogramas']->count() }} subprograma(s)
                                        </div>
                                    </div>

                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach($row['subprogramas'] as $sub)
                                            @php $isActual = $esMismo && ((string)$sub === (string)$expediente->subprograma); @endphp

                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold
                                                {{ $isActual ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $sub }}
                                                @if($isActual) <span class="ml-1">★</span> @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mt-4 px-4 py-3 rounded-lg bg-yellow-50 text-yellow-800 border border-yellow-100 text-sm">
                            No hay asignaciones registradas para la institución del capturista en este año/entidad.
                        </div>
                    @endif
                </div>

                {{-- ===================== DECISIÓN DEL VALIDADOR ===================== --}}
                <div class="border border-gray-200 rounded-xl p-6">
                    <h4 class="text-lg font-semibold text-[#691C32] mb-4">Dictamen del validador</h4>

                    @if ($errors->any())
                        <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">
                            <strong>No se pudo registrar la decisión:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('validador.expedientes.decidir', $expediente) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    Decisión <span class="text-red-600">*</span>
                                </label>
                                <select name="decision" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                               focus:ring-[#691C32] focus:border-[#691C32]">
                                    <option value="">Seleccione...</option>
                                    <option value="aprobar"
                                            {{ old('decision') === 'aprobar' ? 'selected' : '' }}
                                            {{ (isset($coincideAsignacion) && !$coincideAsignacion) ? 'disabled' : '' }}>
                                        Aprobar
                                    </option>
                                    <option value="rechazar" {{ old('decision') === 'rechazar' ? 'selected' : '' }}>Rechazar</option>
                                </select>
                                @if(isset($coincideAsignacion) && !$coincideAsignacion)
                                    <p class="mt-2 text-xs text-red-600 font-semibold">
                                        No puedes aprobar porque el expediente está fuera de asignación (Eje/Programa/Subprograma).
                                    </p>
                                @endif
                                <p class="mt-1 text-[11px] text-gray-500">
                                    Si rechaza, capture observaciones claras para el capturista.
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Observaciones (obligatorio si rechaza)
                                </label>
                                <textarea id="observaciones" name="observaciones" rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                                focus:ring-[#691C32] focus:border-[#691C32]"
                                        placeholder="Escriba observaciones o motivos...">{{ old('observaciones') }}</textarea>

                                <p id="hint-observaciones" class="mt-1 text-[11px] text-gray-500">
                                    Si apruebas, las observaciones son opcionales. Si rechazas, son obligatorias.
                                </p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 mt-5">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-[#691C32] border border-transparent
                                           rounded-md font-semibold text-xs text-white uppercase tracking-widest
                                           hover:bg-[#4e1324] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32] transition">
                                Guardar dictamen
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const decision = document.querySelector('select[name="decision"]');
        const obs = document.getElementById('observaciones');
        const hint = document.getElementById('hint-observaciones');

        const sugerida = @json($observacionSugerida ?? null);
        const fueraAsignacion = @json(isset($coincideAsignacion) ? !$coincideAsignacion : false);

        function sync() {
            const val = decision?.value;
            if (!obs || !hint) return;

            if (val === 'rechazar') {
                obs.setAttribute('required', 'required');
                hint.textContent = 'Para RECHAZAR, escribe observaciones claras (mínimo 10 caracteres).';
                hint.classList.remove('text-gray-500');
                hint.classList.add('text-red-600', 'font-semibold');

                // Autollenar SOLO si está vacío y es fuera de asignación
                if (fueraAsignacion && sugerida && obs.value.trim() === '') {
                    obs.value = sugerida;
                }
            } else {
                obs.removeAttribute('required');
                hint.textContent = 'Si apruebas, las observaciones son opcionales. Si rechazas, son obligatorias.';
                hint.classList.remove('text-red-600', 'font-semibold');
                hint.classList.add('text-gray-500');
            }
        }

        decision?.addEventListener('change', sync);
        sync();
    });
    </script>

</x-app-layout>