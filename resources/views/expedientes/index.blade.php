{{-- resources/views/expedientes/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Mis expedientes técnicos') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                        $estatus = strtolower($expediente->estatus ?? 'borrador');

                                        $badge = match ($estatus) {
                                            'borrador'       => ['bg-gray-100 text-gray-800', 'Borrador'],
                                            'en_validacion'  => ['bg-yellow-100 text-yellow-800', 'En validación'],
                                            'en validacion'  => ['bg-yellow-100 text-yellow-800', 'En validación'],
                                            'aprobado'       => ['bg-green-100 text-green-800', 'Aprobado'],
                                            'rechazado'      => ['bg-red-100 text-red-800', 'Rechazado'],
                                            default          => ['bg-gray-100 text-gray-800', ucfirst($estatus)],
                                        };

                                        $area = $expediente->areaEjecutora;
                                        $areaLabel = $area
                                            ? trim(($area->siglas ? $area->siglas.' - ' : '').$area->nombre)
                                            : '—';

                                        // Placeholder: cuando tengas lógica real, cámbialo por algo tipo:
                                        // $segundaParteCompleta = (bool) $expediente->segunda_parte_completa;
                                        $segundaParteCompleta = false;

                                        // “Enviar a revisión” solo cuando 2 partes completas
                                        $puedeEnviarRevision = $segundaParteCompleta && in_array($estatus, ['borrador','rechazado']);
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
                                            @if($estatus === 'rechazado' && method_exists($expediente, 'ultimaObservacionRechazoCorta'))
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

                                                @if($estatus === 'rechazado')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-red-600 text-white">
                                                        Requiere corrección
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-3 py-2 border text-[11px] text-gray-500">
                                            {{ $expediente->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                        </td>

                                        <td class="px-3 py-2 border">
                                            <div class="flex flex-col gap-2">

                                                {{-- Editar (1ra parte / todo por ahora) --}}
                                                <a href="{{ route('expedientes.edit', $expediente) }}"
                                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                          bg-[#691C32] text-white text-[11px] hover:bg-[#4e1324] transition">
                                                    Editar
                                                </a>

                                                {{-- Ir a 2da parte (por ahora ancla al edit) --}}
                                                <a href="{{ route('expedientes.edit', $expediente) }}#segunda-parte"
                                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                          bg-[#9F2241] text-white text-[11px] hover:bg-[#691C32] transition">
                                                    Ir a 2da parte →
                                                </a>

                                                {{-- Enviar a revisión (bloqueado hasta que 2 partes completas) --}}
                                                <button type="button"
                                                        @disabled(!$puedeEnviarRevision)
                                                        class="inline-flex items-center justify-center px-2 py-1 rounded-md text-[11px] transition
                                                            {{ $puedeEnviarRevision ? 'bg-yellow-600 text-white hover:bg-yellow-700' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }}">
                                                    Enviar a revisión
                                                </button>

                                                {{-- PDF (solo para aprobados) - placeholder --}}
                                                @if($estatus === 'aprobado')
                                                    <button type="button"
                                                            class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                                   bg-green-600 text-white text-[11px] hover:bg-green-700 transition"
                                                            title="Pendiente: ruta de descarga PDF">
                                                        Ver PDF
                                                    </button>
                                                    {{-- Cuando tengas ruta:
                                                    <a href="{{ route('expedientes.pdf', $expediente) }}" ...>Ver PDF</a>
                                                    --}}
                                                @endif

                                                {{-- Eliminar (modal) --}}
                                                <button type="button"
                                                        class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                               bg-red-50 border border-red-200 text-red-700 text-[11px]
                                                               hover:bg-red-100 transition"
                                                        data-open-modal="delete-{{ $expediente->id }}">
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