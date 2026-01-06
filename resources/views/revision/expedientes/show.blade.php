{{-- resources/views/revision/expedientes/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Revisión del expediente</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">{{ session('error') }}</div>
            @endif

            @php
                $u = $expediente->usuario;

                $capturistaNombre = $u
                    ? trim(($u->nombres ?? '').' '.($u->apellido_paterno ?? '').' '.($u->apellido_materno ?? ''))
                    : 'N/A';

                $capturistaInstitucion = $u?->institucion
                    ? trim(($u->institucion->siglas ? $u->institucion->siglas.' - ' : '').($u->institucion->nombre ?? ''))
                    : '—';

                // Último movimiento (prioriza firmado/pendiente/aprobado/rechazado)
                $ultimoMovimiento = $expediente->historiales
                    ? (
                        $expediente->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_FIRMADO)
                        ?? $expediente->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_PENDIENTE_FIRMA)
                        ?? $expediente->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_APROBADO)
                        ?? $expediente->historiales->firstWhere('estado_nuevo', \App\Models\Expediente::ESTADO_RECHAZADO)
                    )
                    : null;

                $rev = $ultimoMovimiento?->usuario;

                $revNombre = $rev
                    ? trim(($rev->nombres ?? '').' '.($rev->apellido_paterno ?? '').' '.($rev->apellido_materno ?? ''))
                    : null;

                $estatus = (string)($expediente->estatus ?? '');

                $enValidacion = $estatus === \App\Models\Expediente::ESTADO_EN_VALIDACION;
                $pendienteFirma = $estatus === \App\Models\Expediente::ESTADO_PENDIENTE_FIRMA;
                $firmado = $estatus === \App\Models\Expediente::ESTADO_FIRMADO;
                $aprobadoLegacy = $estatus === \App\Models\Expediente::ESTADO_APROBADO; // por si hay viejos
                $rechazado = $estatus === \App\Models\Expediente::ESTADO_RECHAZADO;

                $tieneFirmado = !empty($expediente->pdf_firmado_path);

                $urlOriginal = route('expedientes.segunda.pdf', $expediente->id);
                // IMPORTANTE: esta ruta debe existir y apuntar a ExpedienteFirmaController@view
                $urlFirmado  = $tieneFirmado ? route('revision.expedientes.view_firmado', $expediente->id) : null;
            @endphp

            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <a href="{{ route('revision.index') }}"
                       class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        ← Volver a bandeja
                    </a>

                    <div class="flex items-center gap-2">
                        <a href="{{ $urlOriginal }}" target="_blank"
                           class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                            Abrir PDF
                        </a>

                        @if($firmado)
                            <span class="inline-flex items-center px-3 py-2 rounded-md bg-emerald-100 text-emerald-800 text-sm font-semibold">
                                Firmado
                            </span>
                        @elseif($pendienteFirma)
                            <span class="inline-flex items-center px-3 py-2 rounded-md bg-indigo-100 text-indigo-800 text-sm font-semibold">
                                Pendiente de firma
                            </span>
                        @elseif($aprobadoLegacy)
                            <span class="inline-flex items-center px-3 py-2 rounded-md bg-green-100 text-green-800 text-sm font-semibold">
                                Aprobado
                            </span>
                        @elseif($rechazado)
                            <span class="inline-flex items-center px-3 py-2 rounded-md bg-red-100 text-red-800 text-sm font-semibold">
                                Rechazado
                            </span>
                        @elseif($enValidacion)
                            <span class="inline-flex items-center px-3 py-2 rounded-md bg-yellow-100 text-yellow-800 text-sm font-semibold">
                                En validación
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Selector de PDF con estado activo --}}
                <div class="flex items-center gap-2 mb-3">
                    <button id="btnOriginal" type="button"
                            class="px-3 py-1 rounded-md text-sm font-semibold bg-[#691C32] text-white"
                            onclick="setPdf('original')">
                        Ver original
                    </button>

                    @if($tieneFirmado)
                        <button id="btnFirmado" type="button"
                                class="px-3 py-1 rounded-md text-sm font-semibold bg-gray-100 text-gray-800 border border-gray-200"
                                onclick="setPdf('firmado')">
                            Ver firmado
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">
                    {{-- PDF --}}
                    <div class="lg:col-span-3 rounded-xl border border-gray-200 overflow-hidden" style="height: 80vh;">
                        <iframe id="pdfFrame" src="{{ $urlOriginal }}?t={{ time() }}" class="w-full h-full"></iframe>
                    </div>

                    {{-- Panel derecho --}}
                    <div class="lg:col-span-2 space-y-4">

                        {{-- Datos --}}
                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                            <div class="font-semibold text-gray-800 mb-2">Datos</div>

                            <div class="text-sm text-gray-700 space-y-1">
                                <div><span class="font-semibold">Folio:</span> {{ $expediente->folio }}</div>
                                <div><span class="font-semibold">Proyecto:</span> {{ $expediente->nombre_proyecto }}</div>

                                <div>
                                    <span class="font-semibold">Capturista:</span>
                                    {{ $capturistaNombre }}
                                    @if($u?->email)
                                        <span class="text-xs text-gray-500">({{ $u->email }})</span>
                                    @endif
                                </div>

                                <div>
                                    <span class="font-semibold">Institución:</span>
                                    <span class="text-gray-800">{{ $capturistaInstitucion }}</span>
                                </div>

                                <div><span class="font-semibold">Año:</span> {{ $expediente->anio_ejercicio }}</div>
                            </div>
                        </div>

                        {{-- Último movimiento --}}
                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                            <div class="font-semibold text-gray-800 mb-2">Último movimiento</div>

                            @if($ultimoMovimiento)
                                <div class="text-sm text-gray-700 space-y-1">
                                    <div>
                                        <span class="font-semibold">Acción:</span>
                                        {{ $ultimoMovimiento->estado_nuevo }}
                                    </div>

                                    <div>
                                        <span class="font-semibold">Por:</span>
                                        {{ $revNombre ?? 'N/A' }}
                                        @if($rev?->email)
                                            <span class="text-xs text-gray-500">({{ $rev->email }})</span>
                                        @endif
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ optional($ultimoMovimiento->created_at)->format('d/m/Y H:i') }}
                                    </div>

                                    @if($ultimoMovimiento->estado_nuevo === \App\Models\Expediente::ESTADO_RECHAZADO)
                                        <div class="mt-2 text-xs text-gray-600 whitespace-pre-line">
                                            <span class="font-semibold text-gray-800">Observaciones:</span>
                                            {{ $ultimoMovimiento->observaciones }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="text-sm text-gray-600">Sin movimientos registrados aún.</div>
                            @endif
                        </div>

                        {{-- Firmas --}}
                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                            <div class="font-semibold text-gray-800 mb-2">Firmas</div>

                            @if($pendienteFirma)
                                <div class="text-sm text-gray-700 mb-2">
                                    Este expediente ya fue validado. Sube el <span class="font-semibold">PDF firmado</span> (máx 5 MB).
                                </div>

                                <form method="POST"
                                      action="{{ route('revision.expedientes.upload_firmado', $expediente->id) }}"
                                      enctype="multipart/form-data"
                                      class="space-y-3">
                                    @csrf

                                    <input type="file" name="pdf_firmado" accept="application/pdf" class="w-full text-sm" required>

                                    @error('pdf_firmado')
                                        <div class="text-sm text-red-700 font-semibold">{{ $message }}</div>
                                    @enderror

                                    <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 rounded-md bg-[#691C32] text-white text-sm font-semibold hover:bg-[#4e1324]">
                                        Subir PDF firmado
                                    </button>
                                </form>

                            @elseif($firmado)
                                <div class="text-sm text-gray-700 space-y-2">
                                    <div><span class="font-semibold">Estado:</span> Firmado</div>

                                    @if($expediente->pdf_firmado_at)
                                        <div class="text-xs text-gray-500">
                                            Cargado: {{ $expediente->pdf_firmado_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif

                                    <a href="{{ $urlFirmado }}?t={{ time() }}"
                                       target="_blank"
                                       class="w-full inline-flex items-center justify-center px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700">
                                        Ver PDF firmado
                                    </a>

                                    <a href="{{ route('revision.expedientes.download_firmado', $expediente->id) }}"
                                       class="w-full inline-flex items-center justify-center px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                                        Descargar PDF firmado
                                    </a>

                                    {{-- Botón para abrir modal de eliminar --}}
                                    <button type="button"
                                            data-open-modal="deleteFirmado"
                                            class="w-full inline-flex justify-center items-center px-3 py-2 rounded-md bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
                                        Eliminar PDF firmado
                                    </button>

                                    {{-- Modal eliminar firmado --}}
                                    <div id="modal-deleteFirmado" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                                        <div class="absolute inset-0 bg-black/40" data-close-modal="deleteFirmado"></div>

                                        <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 p-6">
                                            <div class="flex items-start justify-between gap-4">
                                                <div>
                                                    <h4 class="text-lg font-bold text-[#691C32]">Eliminar PDF firmado</h4>
                                                    <p class="mt-1 text-sm text-gray-600">
                                                        Esta acción eliminará el PDF firmado y el expediente volverá a <b>Pendiente de firma</b>.
                                                    </p>
                                                </div>
                                                <button type="button" class="text-gray-400 hover:text-gray-600" data-close-modal="deleteFirmado">✕</button>
                                            </div>

                                            <div class="mt-4 rounded-xl border bg-gray-50 p-4 text-sm">
                                                <div class="font-semibold text-gray-800">{{ $expediente->folio }}</div>
                                                <div class="text-gray-600 mt-1">{{ $expediente->nombre_proyecto }}</div>
                                            </div>

                                            <div class="mt-5 flex items-center justify-end gap-2">
                                                <button type="button"
                                                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition"
                                                        data-close-modal="deleteFirmado">
                                                    Cancelar
                                                </button>

                                                <form method="POST" action="{{ route('revision.expedientes.delete_firmado', $expediente->id) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition"
                                                            onclick="this.disabled=true; this.innerText='Eliminando...'; this.form.submit();">
                                                        Sí, eliminar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-500">
                                        Nota: El PDF original (sin firma) siempre se mantiene.
                                    </p>
                                </div>

                            @else
                                <div class="text-sm text-gray-600">
                                    Este expediente aún no está en etapa de firma.
                                </div>
                            @endif
                        </div>

                        {{-- Acciones de revisión (solo si está en validación) --}}
                        @if($enValidacion)
                            <button type="button"
                                    data-open-modal="rechazar"
                                    class="w-full inline-flex justify-center items-center px-3 py-2 rounded-md bg-red-600 text-white text-sm font-semibold hover:bg-red-700">
                                Rechazar
                            </button>

                            {{-- Modal Rechazar --}}
                            <div id="modal-rechazar" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                                <div class="absolute inset-0 bg-black/40" data-close-modal="rechazar"></div>

                                <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-xl border border-gray-200 p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="text-lg font-bold text-[#691C32]">Rechazar expediente</h4>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Escribe observaciones obligatorias para poder rechazar.
                                            </p>
                                        </div>
                                        <button type="button" class="text-gray-400 hover:text-gray-600" data-close-modal="rechazar">✕</button>
                                    </div>

                                    @if ($errors->any())
                                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                                            <div class="font-semibold mb-1">Corrige lo siguiente:</div>
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach ($errors->all() as $e)
                                                    <li>{{ $e }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <form id="form-rechazar" method="POST" action="{{ route('revision.rechazar', $expediente->id) }}" class="mt-4 space-y-3">
                                        @csrf

                                        <textarea id="observaciones"
                                                  name="observaciones"
                                                  rows="7"
                                                  class="w-full rounded-md border-gray-300 focus:border-[#691C32] focus:ring-[#691C32]"
                                                  placeholder="Escribe las observaciones...">{{ old('observaciones') }}</textarea>

                                        <div id="obs-error" class="hidden text-sm text-red-700 font-semibold">
                                            Las observaciones son obligatorias para rechazar.
                                        </div>

                                        <div class="mt-4 flex items-center justify-end gap-2">
                                            <button type="button"
                                                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition"
                                                    data-close-modal="rechazar">
                                                Cancelar
                                            </button>

                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-md hover:bg-red-700 transition">
                                                Confirmar rechazo
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <button type="button"
                                    data-open-modal="aprobar"
                                    class="w-full inline-flex justify-center items-center px-3 py-2 rounded-md bg-green-600 text-white text-sm font-semibold hover:bg-green-700">
                                Aprobar (pasar a firma)
                            </button>

                            {{-- Modal Aprobar --}}
                            <div id="modal-aprobar" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                                <div class="absolute inset-0 bg-black/40" data-close-modal="aprobar"></div>

                                <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl border border-gray-200 p-6">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h4 class="text-lg font-bold text-[#691C32]">Validar expediente</h4>
                                            <p class="mt-1 text-sm text-gray-600">
                                                Al aprobar, el expediente pasará a <b>Pendiente de firma</b>.
                                            </p>
                                        </div>
                                        <button type="button" class="text-gray-400 hover:text-gray-600" data-close-modal="aprobar">✕</button>
                                    </div>

                                    <div class="mt-4 rounded-xl border bg-gray-50 p-4 text-sm">
                                        <div class="font-semibold text-gray-800">{{ $expediente->folio }}</div>
                                        <div class="text-gray-600 mt-1">{{ $expediente->nombre_proyecto }}</div>
                                    </div>

                                    <div class="mt-5 flex items-center justify-end gap-2">
                                        <button type="button"
                                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-xs font-semibold rounded-md hover:bg-gray-200 transition"
                                                data-close-modal="aprobar">
                                            Cancelar
                                        </button>

                                        <form method="POST" action="{{ route('revision.aprobar', $expediente->id) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-xs font-semibold rounded-md hover:bg-green-700 transition"
                                                    onclick="this.disabled=true; this.innerText='Aprobando...'; this.form.submit();">
                                                Sí, validar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl border border-gray-200 p-4 bg-gray-50 text-sm text-gray-700">
                                Las acciones de validación están deshabilitadas porque este expediente ya fue atendido.
                            </div>
                        @endif

                        {{-- Últimas observaciones --}}
                        <div class="rounded-xl border border-gray-200 p-4 bg-gray-50">
                            <div class="font-semibold text-gray-800 mb-2">Últimas observaciones</div>
                            <div class="text-sm text-gray-700 whitespace-pre-line">
                                {{ $expediente->ultimaObservacionRechazo() ?? 'Sin observaciones previas.' }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>

<script>
(function () {
    const urlOriginal = @json($urlOriginal);
    const urlFirmado  = @json($urlFirmado);

    function setActive(which) {
        const b1 = document.getElementById('btnOriginal');
        const b2 = document.getElementById('btnFirmado');

        if (b1) {
            const on = (which === 'original');
            b1.classList.toggle('bg-[#691C32]', on);
            b1.classList.toggle('text-white', on);
            b1.classList.toggle('bg-gray-100', !on);
            b1.classList.toggle('text-gray-800', !on);
            b1.classList.toggle('border', !on);
            b1.classList.toggle('border-gray-200', !on);
        }

        if (b2) {
            const on = (which === 'firmado');
            b2.classList.toggle('bg-[#691C32]', on);
            b2.classList.toggle('text-white', on);
            b2.classList.toggle('bg-gray-100', !on);
            b2.classList.toggle('text-gray-800', !on);
            b2.classList.toggle('border', !on);
            b2.classList.toggle('border-gray-200', !on);
        }
    }

    window.setPdf = function(which) {
        const frame = document.getElementById('pdfFrame');
        if (!frame) return;

        if (which === 'firmado') {
            if (!urlFirmado) return;
            frame.src = urlFirmado + '?t=' + Date.now(); // cache buster
            setActive('firmado');
        } else {
            frame.src = urlOriginal + '?t=' + Date.now(); // cache buster
            setActive('original');
        }
    }

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

    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', () => openModal(btn.dataset.openModal));
    });

    document.querySelectorAll('[data-close-modal]').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.closeModal));
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            ['rechazar','aprobar','deleteFirmado'].forEach(k => closeModal(k));
        }
    });

    const form = document.getElementById('form-rechazar');
    if (form) {
        form.addEventListener('submit', function (e) {
            const textarea = document.getElementById('observaciones');
            const msg = document.getElementById('obs-error');
            const val = (textarea?.value || '').trim();

            if (!val) {
                e.preventDefault();
                msg?.classList.remove('hidden');
                textarea?.focus();
                return;
            }
            msg?.classList.add('hidden');
        });
    }

    @if($errors->any())
        openModal('rechazar');
    @endif

    // default activo
    setActive('original');
})();
</script>

</x-app-layout>