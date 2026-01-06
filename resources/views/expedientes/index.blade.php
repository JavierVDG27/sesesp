{{-- resources/views/expedientes/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Mis expedientes técnicos') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Toast success --}}
            @if (session('success'))
                <div id="toast-success"
                     class="fixed top-6 right-6 z-50 max-w-md rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 shadow-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold">Listo</div>
                            <div class="text-sm">{{ session('success') }}</div>
                        </div>
                        <button type="button" class="text-green-800/60 hover:text-green-900"
                                onclick="document.getElementById('toast-success')?.remove()">
                            ✕
                        </button>
                    </div>
                </div>
                <script>
                    setTimeout(() => document.getElementById('toast-success')?.remove(), 4500);
                </script>
            @endif

            {{-- Toast error --}}
            @if (session('error'))
                <div id="toast-error"
                     class="fixed top-6 right-6 z-50 max-w-md rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-800 shadow-lg">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="font-semibold">Atención</div>
                            <div class="text-sm">{{ session('error') }}</div>
                        </div>
                        <button type="button" class="text-red-800/60 hover:text-red-900"
                                onclick="document.getElementById('toast-error')?.remove()">
                            ✕
                        </button>
                    </div>
                </div>
                <script>
                    setTimeout(() => document.getElementById('toast-error')?.remove(), 6000);
                </script>
            @endif

            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-6 md:p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">
                            Mis expedientes
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Consulta tus expedientes, revisa estatus y continúa su captura.
                        </p>
                    </div>

                    <div class="flex justify-end">
                        <a href="{{ route('expedientes.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-[#691C32] border border-transparent
                                  rounded-md font-semibold text-xs text-white uppercase tracking-widest
                                  hover:bg-[#4e1324] active:bg-[#4e1324] focus:outline-none
                                  focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32] transition">
                            + Nuevo expediente
                        </a>
                    </div>
                </div>

                @if ($expedientes->isEmpty())
                    <div class="py-10 text-center text-gray-500 text-sm">
                        Aún no has registrado ningún expediente.
                        <a href="{{ route('expedientes.create') }}" class="text-[#691C32] font-semibold hover:underline">
                            Crear el primero →
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs md:text-sm border border-gray-200">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 border">Folio</th>
                                    <th class="px-3 py-2 border">Nombre del proyecto</th>
                                    <th class="px-3 py-2 border hidden md:table-cell">Área ejecutora</th>
                                    <th class="px-3 py-2 border hidden md:table-cell">Tipo recurso</th>
                                    <th class="px-3 py-2 border">Año</th>
                                    <th class="px-3 py-2 border">Estatus</th>
                                    <th class="px-3 py-2 border">Última act.</th>
                                    <th class="px-3 py-2 border w-44">Acciones</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($expedientes as $expediente)
                                    @php
                                        // Normalizamos a minúsculas para comparar
                                        $estatusRaw = (string)($expediente->estatus ?? 'borrador');
                                        $estatus = strtolower($estatusRaw);

                                        // Tus estados pueden venir con espacios o guiones, lo manejamos:
                                        $esEnValidacion = in_array($estatus, ['en_validacion','en validacion'], true);
                                        $esPendienteFirma = in_array($estatus, ['pendiente_firma','pendiente firma'], true);
                                        $esFirmado = in_array($estatus, ['firmado'], true);
                                        $esAprobado = in_array($estatus, ['aprobado'], true);
                                        $esRechazado = in_array($estatus, ['rechazado'], true);
                                        $esBorrador = in_array($estatus, ['borrador'], true);

                                        $badge = match (true) {
                                            $esBorrador => ['bg-gray-100 text-gray-800', 'Borrador'],
                                            $esEnValidacion => ['bg-yellow-100 text-yellow-800', 'En validación'],
                                            $esPendienteFirma => ['bg-purple-100 text-purple-800', 'Pendiente de firma'],
                                            $esFirmado => ['bg-indigo-100 text-indigo-800', 'Firmado'],
                                            $esAprobado => ['bg-green-100 text-green-800', 'Aprobado'],
                                            $esRechazado => ['bg-red-100 text-red-800', 'Rechazado'],
                                            default => ['bg-gray-100 text-gray-800', ucfirst($estatusRaw)],
                                        };

                                        $area = $expediente->areaEjecutora;
                                        $areaLabel = $area
                                            ? trim(($area->siglas ? $area->siglas.' - ' : '').$area->nombre)
                                            : '—';

                                        // ✅ Regla: si está en validación / pendiente firma / firmado / aprobado => NO se edita, solo ver PDF
                                        $bloqueado = ($esEnValidacion || $esPendienteFirma || $esFirmado || $esAprobado);

                                        // ✅ Ver PDF original en esos estados (y si quieres también en rechazado/borrador puedes dejarlo)
                                        $puedeVerPdf = ($esEnValidacion || $esPendienteFirma || $esFirmado || $esAprobado);

                                        // ✅ Solo se puede editar/continuar captura en borrador o rechazado
                                        $puedeEditar = ($esBorrador || $esRechazado);
                                    @endphp

                                    <tr class="hover:bg-gray-50 align-top">
                                        <td class="px-3 py-2 border font-mono text-xs">
                                            {{ $expediente->folio }}
                                        </td>

                                        <td class="px-3 py-2 border">
                                            <div class="font-semibold text-gray-800 truncate max-w-xs">
                                                {{ $expediente->nombre_proyecto }}
                                            </div>

                                            {{-- Mobile meta --}}
                                            <div class="md:hidden text-[11px] text-gray-500 mt-1">
                                                <div><span class="font-semibold">Área:</span> {{ $areaLabel }}</div>
                                                <div><span class="font-semibold">Tipo:</span> {{ $expediente->tipo_recurso ?: '—' }}</div>
                                            </div>

                                            {{-- Si fue rechazado: mostrar última observación --}}
                                            @if($esRechazado && method_exists($expediente, 'ultimaObservacionRechazoCorta'))
                                                <div class="mt-2 rounded-md border border-red-200 bg-red-50 p-2 text-[11px] text-red-800">
                                                    <span class="font-semibold">Observación:</span>
                                                    {{ $expediente->ultimaObservacionRechazoCorta(160) ?? 'Sin observaciones registradas.' }}
                                                </div>
                                            @endif
                                        </td>

                                        <td class="px-3 py-2 border hidden md:table-cell">
                                            <span class="text-xs text-gray-700">
                                                {{ $areaLabel }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-2 border hidden md:table-cell">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] bg-gray-100 text-gray-700">
                                                {{ $expediente->tipo_recurso ?: '—' }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-2 border text-center">
                                            {{ $expediente->anio_ejercicio }}
                                        </td>

                                        <td class="px-3 py-2 border">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $badge[0] }}">
                                                    {{ $badge[1] }}
                                                </span>

                                                @if($esRechazado)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-red-600 text-white">
                                                        Requiere corrección
                                                    </span>
                                                @endif

                                                @if($esEnValidacion)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-yellow-600 text-white">
                                                        En revisión
                                                    </span>
                                                @endif

                                                @if($esPendienteFirma)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-purple-600 text-white">
                                                        Pendiente de firma
                                                    </span>
                                                @endif

                                                @if($esFirmado)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-600 text-white">
                                                        Firmado
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-3 py-2 border text-[11px] text-gray-500">
                                            {{ $expediente->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                        </td>

                                        <td class="px-3 py-2 border">
                                            <div class="flex flex-col gap-2">

                                                {{-- Editar (1ra parte) --}}
                                                <a href="{{ $puedeEditar ? route('expedientes.edit', $expediente) : '#' }}"
                                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md text-[11px] transition
                                                          {{ $puedeEditar ? 'bg-[#691C32] text-white hover:bg-[#4e1324]' : 'bg-gray-200 text-gray-500 cursor-not-allowed pointer-events-none' }}">
                                                    Editar
                                                </a>

                                                {{-- Ir a 2da parte --}}
                                                <a href="{{ $puedeEditar ? route('expedientes.segunda.edit', $expediente) : '#' }}"
                                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md text-[11px] transition
                                                          {{ $puedeEditar ? 'bg-[#9F2241] text-white hover:bg-[#691C32]' : 'bg-gray-200 text-gray-500 cursor-not-allowed pointer-events-none' }}">
                                                    Ir a 2da parte →
                                                </a>

                                                {{-- Ver PDF original --}}
                                                @if($puedeVerPdf)
                                                    <a href="{{ route('expedientes.segunda.pdf', $expediente->id) }}"
                                                       target="_blank"
                                                       class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                              bg-blue-600 text-white text-[11px] hover:bg-blue-700 transition">
                                                        Ver PDF
                                                    </a>
                                                @endif

                                                {{-- Eliminar (solo si NO está bloqueado) --}}
                                                <button type="button"
                                                        {{ $bloqueado ? 'disabled' : '' }}
                                                        class="inline-flex items-center justify-center px-2 py-1 rounded-md text-[11px] transition
                                                               {{ $bloqueado ? 'bg-gray-100 border border-gray-200 text-gray-400 cursor-not-allowed' : 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100' }}"
                                                        @if(!$bloqueado) data-open-modal="delete-{{ $expediente->id }}" @endif>
                                                    Eliminar
                                                </button>
                                            </div>

                                            {{-- MODAL ELIMINAR --}}
                                            <div id="modal-delete-{{ $expediente->id }}"
                                                 class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                                                {{-- overlay --}}
                                                <div class="absolute inset-0 bg-black/40"
                                                     data-close-modal="delete-{{ $expediente->id }}"></div>

                                                {{-- content --}}
                                                <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 p-6">
                                                    <div class="flex items-start justify-between gap-4">
                                                        <div>
                                                            <h4 class="text-lg font-bold text-[#691C32]">Eliminar expediente</h4>
                                                            <p class="mt-1 text-sm text-gray-600">
                                                                ¿Seguro que deseas eliminar este expediente?
                                                            </p>
                                                        </div>

                                                        <button type="button"
                                                                class="text-gray-400 hover:text-gray-600"
                                                                data-close-modal="delete-{{ $expediente->id }}">
                                                            ✕
                                                        </button>
                                                    </div>

                                                    <div class="mt-4 rounded-xl border bg-gray-50 p-4 text-sm">
                                                        <div class="font-semibold text-gray-800">{{ $expediente->folio }}</div>
                                                        <div class="text-gray-600 mt-1">
                                                            {{ $expediente->nombre_proyecto }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 mt-2">
                                                            Área ejecutora: <span class="font-semibold">{{ $areaLabel }}</span>
                                                        </div>
                                                    </div>

                                                    <div class="mt-5 flex items-center justify-end gap-2">
                                                        <button type="button"
                                                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition"
                                                                data-close-modal="delete-{{ $expediente->id }}">
                                                            Cancelar
                                                        </button>

                                                        <form action="{{ route('expedientes.destroy', $expediente) }}" method="POST">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition">
                                                                Sí, eliminar
                                                            </button>
                                                        </form>
                                                    </div>

                                                    <p class="mt-3 text-[11px] text-gray-500">
                                                        Esta acción no se puede deshacer.
                                                    </p>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $expedientes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- JS modales --}}
    <script>
        (function () {
            const openers = document.querySelectorAll('[data-open-modal]');
            const closers = document.querySelectorAll('[data-close-modal]');

            function openModal(key) {
                const el = document.getElementById('modal-' + key);
                if (!el) return;
                el.classList.remove('hidden');
                el.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }

            function closeModal(key) {
                const el = document.getElementById('modal-' + key);
                if (!el) return;
                el.classList.add('hidden');
                el.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }

            openers.forEach(btn => {
                btn.addEventListener('click', () => openModal(btn.dataset.openModal));
            });

            closers.forEach(btn => {
                btn.addEventListener('click', () => closeModal(btn.dataset.closeModal));
            });

            document.addEventListener('keydown', (e) => {
                if (e.key !== 'Escape') return;
                document.querySelectorAll('[id^="modal-delete-"]').forEach(m => {
                    if (!m.classList.contains('hidden')) {
                        const key = m.id.replace('modal-', '');
                        closeModal(key);
                    }
                });
            });
        })();
    </script>
</x-app-layout>