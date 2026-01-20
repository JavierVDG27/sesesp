<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Vista previa del PDF</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
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
                                onclick="document.getElementById('toast-success')?.remove()">✕</button>
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
                                onclick="document.getElementById('toast-error')?.remove()">✕</button>
                    </div>
                </div>
                <script>
                    setTimeout(() => document.getElementById('toast-error')?.remove(), 6000);
                </script>
            @endif

            @php
                $estatus = strtolower((string)($expediente->estatus ?? 'borrador'));
                $estatus = str_replace('_', ' ', $estatus);

                // Bloqueo de captura
                $bloqueado = in_array($estatus, ['en validacion', 'aprobado'], true);

                // Consideramos borrador todo lo que no sea aprobado / pendiente firma / firmado
                $esBorrador = !in_array($estatus, ['aprobado', 'pendiente firma', 'firmado'], true);
            @endphp

            <div class="bg-white shadow-lg rounded-2xl p-6">

                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('expedientes.segunda.edit', $expediente->id) }}"
                           class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            ← Volver a captura
                        </a>

                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Volver a mis expedientes
                        </a>
                    </div>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('expedientes.segunda.pdf', $expediente->id) }}"
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                            Abrir PDF en nueva pestaña
                        </a>

                        {{-- Si ya está en validación o aprobado, no permitir enviar de nuevo --}}
                        @if($bloqueado)
                            <span class="inline-flex items-center px-3 py-2 rounded-md bg-yellow-100 text-yellow-800 text-sm font-semibold">
                                {{ $estatus === 'aprobado' ? 'Expediente aprobado' : 'Expediente en revisión' }}
                            </span>
                        @else
                            <form method="POST"
                                  action="{{ route('expedientes.segunda.enviar', $expediente->id) }}"
                                  onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerText='Enviando...';">
                                @csrf
                                <button type="submit"
                                        class="inline-flex items-center px-3 py-2 rounded-md text-white text-sm font-semibold
                                        {{ $check['ok'] ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed' }}"
                                        {{ $check['ok'] ? '' : 'disabled' }}>
                                    Enviar a revisión
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Aviso de modo borrador / definitivo --}}
                @if($esBorrador)
                    <div class="mb-4 rounded-xl border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                        <div class="font-semibold mb-1">Expediente en modo borrador</div>
                        <p>
                            La vista previa del PDF se genera con una marca de agua 
                            <span class="font-bold">“BORRADOR”</span> en todas las páginas.
                            Cuando el expediente sea <span class="font-semibold">aprobado</span>,
                            el PDF dejará de mostrar esta marca.
                        </p>
                    </div>
                @else
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <div class="font-semibold mb-1">Expediente definitivo</div>
                        <p>
                            Este expediente ya no está en modo borrador. El PDF se genera
                            <span class="font-semibold">sin marca de agua</span>.
                        </p>
                    </div>
                @endif

                {{-- Checklist --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 mb-4">
                    <div class="font-semibold text-gray-800 mb-2">Checklist para enviar</div>
                    <ul class="text-sm space-y-1">
                        @foreach($check['items'] as $it)
                            <li class="flex items-center justify-between">
                                <span>{{ $it['label'] }}</span>
                                <span class="font-semibold {{ $it['ok'] ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $it['ok'] ? 'OK' : 'FALTA' }}
                                </span>
                            </li>
                        @endforeach
                    </ul>

                    @if($bloqueado)
                        <div class="mt-3 text-xs text-gray-600">
                            Este expediente ya fue enviado a revisión, por lo que la captura queda bloqueada hasta que sea aprobado o rechazado.
                        </div>
                    @endif
                </div>

                {{-- PDF embebido --}}
                <div class="rounded-xl border border-gray-200 overflow-hidden" style="height: 80vh;">
                    <iframe src="{{ route('expedientes.segunda.pdf', $expediente->id) }}" class="w-full h-full"></iframe>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
