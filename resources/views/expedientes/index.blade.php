{{-- resources/views/expedientes/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-white leading-tight">
                {{ __('Mis expedientes técnicos') }}
            </h2>

            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-white ring-1 ring-white/15 text-xs">
                    <span class="h-2 w-2 rounded-full bg-white/70"></span>
                    Capturista
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Toast success --}}
            @if (session('success'))
                <div id="toast-success"
                     class="fixed top-6 right-6 z-50 max-w-md rounded-2xl border border-green-200 bg-white px-4 py-3 text-green-900 shadow-xl">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 h-9 w-9 rounded-xl bg-green-100 text-green-800 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-extrabold">Listo</div>
                            <div class="text-sm text-green-800/90">{{ session('success') }}</div>
                        </div>
                        <button type="button"
                                class="text-gray-400 hover:text-gray-600"
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
                     class="fixed top-6 right-6 z-50 max-w-md rounded-2xl border border-red-200 bg-white px-4 py-3 text-red-900 shadow-xl">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 h-9 w-9 rounded-xl bg-red-100 text-red-800 flex items-center justify-center shrink-0">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="font-extrabold">Atención</div>
                            <div class="text-sm text-red-800/90">{{ session('error') }}</div>
                        </div>
                        <button type="button"
                                class="text-gray-400 hover:text-gray-600"
                                onclick="document.getElementById('toast-error')?.remove()">
                            ✕
                        </button>
                    </div>
                </div>
                <script>
                    setTimeout(() => document.getElementById('toast-error')?.remove(), 6000);
                </script>
            @endif

            <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100">
                {{-- Header card --}}
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <div class="shrink-0">
                                <div class="h-12 w-12 rounded-2xl bg-[#691C32]/10 ring-1 ring-[#691C32]/15 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-[#691C32]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M14 2v6h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8 13h8M8 17h6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                    </svg>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900">
                                    <span class="text-[#691C32]">Mis expedientes</span>
                                </h3>
                                <p class="text-sm sm:text-base text-gray-600 mt-1 leading-relaxed">
                                    Consulta tus expedientes, revisa estatus y continúa su captura.
                                </p>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-50 text-gray-700 ring-1 ring-gray-200 text-xs">
                                        <svg class="h-4 w-4 text-gray-500" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                        Listado y seguimiento
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <a href="{{ route('expedientes.create') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#691C32] border border-transparent
                                      rounded-xl font-semibold text-xs text-white uppercase tracking-widest
                                      hover:bg-[#4e1324] active:bg-[#4e1324] focus:outline-none
                                      focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32] transition shadow-sm">
                                <span class="text-base leading-none">+</span>
                                Nuevo expediente
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    @if ($expedientes->isEmpty())
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-10 text-center">
                            <div class="mx-auto h-12 w-12 rounded-2xl bg-white ring-1 ring-gray-200 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-700" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M14 2v6h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div class="mt-3 text-sm font-semibold text-gray-800">
                                Aún no has registrado ningún expediente.
                            </div>
                            <div class="mt-1 text-sm text-gray-600">
                                Inicia uno nuevo para comenzar la captura.
                            </div>
                            <div class="mt-5">
                                <a href="{{ route('expedientes.create') }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#691C32] text-white text-sm font-semibold rounded-xl hover:bg-[#4e1324] transition shadow-sm">
                                    Crear el primero
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="overflow-hidden rounded-2xl border border-gray-200">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs md:text-sm">
                                    <thead class="bg-gray-50 text-gray-700">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200">Folio</th>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200">Nombre del proyecto</th>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200 hidden md:table-cell">Área ejecutora</th>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200 hidden md:table-cell">Tipo recurso</th>
                                            <th class="px-4 py-3 text-center font-bold border-b border-gray-200">Año</th>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200">Estatus</th>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200">Última act.</th>
                                            <th class="px-4 py-3 text-left font-bold border-b border-gray-200 w-52">Acciones</th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-100 bg-white">
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

                                                // Regla: si está en validación / pendiente firma / firmado / aprobado => NO se edita, solo ver PDF
                                                $bloqueado = ($esEnValidacion || $esPendienteFirma || $esFirmado || $esAprobado);

                                                // Ver PDF original en esos estados (y si quieres también en rechazado/borrador puedes dejarlo)
                                                $puedeVerPdf = ($esEnValidacion || $esPendienteFirma || $esFirmado || $esAprobado);

                                                // Solo se puede editar/continuar captura en borrador o rechazado
                                                $puedeEditar = ($esBorrador || $esRechazado);
                                            @endphp

                                            <tr class="hover:bg-gray-50/70 align-top">
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-xl bg-gray-50 ring-1 ring-gray-200 px-2.5 py-1 font-mono text-[11px] text-gray-800">
                                                        {{ $expediente->folio }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3">
                                                    <div class="font-semibold text-gray-900 max-w-sm">
                                                        <div class="truncate">
                                                            {{ $expediente->nombre_proyecto }}
                                                        </div>
                                                    </div>

                                                    {{-- Mobile meta --}}
                                                    <div class="md:hidden text-[11px] text-gray-500 mt-2 space-y-0.5">
                                                        <div><span class="font-semibold text-gray-700">Área:</span> {{ $areaLabel }}</div>
                                                        <div><span class="font-semibold text-gray-700">Tipo:</span> {{ $expediente->tipo_recurso ?: '—' }}</div>
                                                    </div>

                                                    {{-- Si fue rechazado: mostrar última observación --}}
                                                    @if($esRechazado && method_exists($expediente, 'ultimaObservacionRechazoCorta'))
                                                        <div class="mt-3 rounded-2xl border border-red-200 bg-red-50 p-3 text-[11px] text-red-800">
                                                            <div class="flex items-start gap-2">
                                                                <div class="mt-0.5 h-7 w-7 rounded-xl bg-red-100 text-red-800 flex items-center justify-center shrink-0">
                                                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                        <path d="M12 9v4m0 4h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    </svg>
                                                                </div>
                                                                <div class="min-w-0">
                                                                    <span class="font-semibold">Observación:</span>
                                                                    <span class="text-red-800/90">
                                                                        {{ $expediente->ultimaObservacionRechazoCorta(160) ?? 'Sin observaciones registradas.' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>

                                                <td class="px-4 py-3 hidden md:table-cell">
                                                    <span class="text-sm text-gray-700">
                                                        {{ $areaLabel }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3 hidden md:table-cell">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-700 ring-1 ring-gray-200">
                                                        {{ $expediente->tipo_recurso ?: '—' }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white ring-1 ring-gray-200 text-gray-800">
                                                        {{ $expediente->anio_ejercicio }}
                                                    </span>
                                                </td>

                                                <td class="px-4 py-3">
                                                    <div class="flex items-center gap-2 flex-wrap">
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $badge[0] }}">
                                                            {{ $badge[1] }}
                                                        </span>

                                                        @if($esRechazado)
                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-600 text-white">
                                                                Requiere corrección
                                                            </span>
                                                        @endif

                                                        @if($esEnValidacion)
                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-yellow-600 text-white">
                                                                En revisión
                                                            </span>
                                                        @endif

                                                        @if($esPendienteFirma)
                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-purple-600 text-white">
                                                                Pendiente de firma
                                                            </span>
                                                        @endif

                                                        @if($esFirmado)
                                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-indigo-600 text-white">
                                                                Firmado
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>

                                                <td class="px-4 py-3 text-[11px] text-gray-500">
                                                    <div class="font-semibold text-gray-700 text-xs">Actualizado</div>
                                                    <div class="mt-0.5">
                                                        {{ $expediente->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                                    </div>
                                                </td>

                                                <td class="px-4 py-3">
                                                    <div class="grid grid-cols-1 gap-2">

                                                        {{-- Editar (1ra parte) --}}
                                                        <a href="{{ $puedeEditar ? route('expedientes.edit', $expediente) : '#' }}"
                                                           class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-[11px] font-semibold transition shadow-sm
                                                                  {{ $puedeEditar ? 'bg-[#691C32] text-white hover:bg-[#4e1324]' : 'bg-gray-100 text-gray-400 ring-1 ring-gray-200 cursor-not-allowed pointer-events-none' }}">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M12 20h9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                            Editar
                                                        </a>

                                                        {{-- Ir a 2da parte --}}
                                                        <a href="{{ $puedeEditar ? route('expedientes.segunda.edit', $expediente) : '#' }}"
                                                           class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-[11px] font-semibold transition shadow-sm
                                                                  {{ $puedeEditar ? 'bg-[#9F2241] text-white hover:bg-[#691C32]' : 'bg-gray-100 text-gray-400 ring-1 ring-gray-200 cursor-not-allowed pointer-events-none' }}">
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M5 12h14M13 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                            </svg>
                                                            Ir a 2da parte
                                                        </a>

                                                        {{-- Ver PDF original --}}
                                                        @if($puedeVerPdf)
                                                            <a href="{{ route('expedientes.segunda.pdf', $expediente->id) }}"
                                                               target="_blank"
                                                               class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-blue-600 text-white text-[11px] font-semibold hover:bg-blue-700 transition shadow-sm">
                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M14 2v6h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                                    <path d="M8 13h8" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                </svg>
                                                                Ver PDF
                                                            </a>
                                                        @endif

                                                        {{-- Eliminar (solo si NO está bloqueado) --}}
                                                        <button type="button"
                                                                {{ $bloqueado ? 'disabled' : '' }}
                                                                class="inline-flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-[11px] font-semibold transition
                                                                       {{ $bloqueado ? 'bg-gray-50 border border-gray-200 text-gray-400 cursor-not-allowed' : 'bg-red-50 border border-red-200 text-red-700 hover:bg-red-100' }}"
                                                                @if(!$bloqueado) data-open-modal="delete-{{ $expediente->id }}" @endif>
                                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                <path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                <path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                <path d="M10 11v6M14 11v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                            </svg>
                                                            Eliminar
                                                        </button>
                                                    </div>

                                                    {{-- MODAL ELIMINAR --}}
                                                    <div id="modal-delete-{{ $expediente->id }}"
                                                         class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                                                        {{-- overlay --}}
                                                        <div class="absolute inset-0 bg-black/50"
                                                             data-close-modal="delete-{{ $expediente->id }}"></div>

                                                        {{-- content --}}
                                                        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-2xl border border-gray-200">
                                                            <div class="p-6">
                                                                <div class="flex items-start justify-between gap-4">
                                                                    <div class="flex items-start gap-3">
                                                                        <div class="mt-0.5 h-10 w-10 rounded-2xl bg-red-50 ring-1 ring-red-100 text-red-700 flex items-center justify-center shrink-0">
                                                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                                <path d="M3 6h18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                                <path d="M8 6V4h8v2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                                                            </svg>
                                                                        </div>
                                                                        <div>
                                                                            <h4 class="text-lg font-extrabold text-[#691C32]">Eliminar expediente</h4>
                                                                            <p class="mt-1 text-sm text-gray-600">
                                                                                ¿Seguro que deseas eliminar este expediente?
                                                                            </p>
                                                                        </div>
                                                                    </div>

                                                                    <button type="button"
                                                                            class="text-gray-400 hover:text-gray-600"
                                                                            data-close-modal="delete-{{ $expediente->id }}">
                                                                        ✕
                                                                    </button>
                                                                </div>

                                                                <div class="mt-5 rounded-2xl border border-gray-200 bg-gray-50 p-4 text-sm">
                                                                    <div class="flex items-start justify-between gap-3">
                                                                        <div class="min-w-0">
                                                                            <div class="font-semibold text-gray-900">{{ $expediente->folio }}</div>
                                                                            <div class="text-gray-600 mt-1 line-clamp-2">
                                                                                {{ $expediente->nombre_proyecto }}
                                                                            </div>
                                                                        </div>
                                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-red-50 text-red-700 border border-red-100">
                                                                            Eliminar
                                                                        </span>
                                                                    </div>

                                                                    <div class="text-xs text-gray-500 mt-3">
                                                                        Área ejecutora: <span class="font-semibold text-gray-700">{{ $areaLabel }}</span>
                                                                    </div>
                                                                </div>

                                                                <p class="mt-4 text-[11px] text-gray-500">
                                                                    Esta acción no se puede deshacer.
                                                                </p>
                                                            </div>

                                                            <div class="px-6 py-4 border-t border-gray-100 bg-white rounded-b-2xl flex items-center justify-end gap-2">
                                                                <button type="button"
                                                                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-xl hover:bg-gray-200 transition"
                                                                        data-close-modal="delete-{{ $expediente->id }}">
                                                                    Cancelar
                                                                </button>

                                                                <form action="{{ route('expedientes.destroy', $expediente) }}" method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
                                                                        Sí, eliminar
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-5">
                            {{ $expedientes->links() }}
                        </div>
                    @endif
                </div>
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

            document.addEventListener('click', (e) => {
                const t = e.target;
                if (!(t instanceof HTMLElement)) return;
                if (t.hasAttribute('data-close-modal')) {
                    closeModal(t.getAttribute('data-close-modal'));
                }
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