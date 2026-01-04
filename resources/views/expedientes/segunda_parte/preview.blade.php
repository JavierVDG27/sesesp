<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">Vista previa del PDF</h2>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-6">

                <div class="flex flex-wrap items-center justify-between gap-2 mb-4">
                    <a href="{{ route('expedientes.segunda.edit', $expediente->id) }}"
                       class="inline-flex items-center px-3 py-2 rounded-md border border-gray-200 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        ← Volver a captura
                    </a>

                    <div class="flex items-center gap-2">
                        <a href="{{ route('expedientes.segunda.pdf', $expediente->id) }}"
                           target="_blank"
                           class="inline-flex items-center px-3 py-2 rounded-md bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                            Abrir PDF en nueva pestaña
                        </a>

                        <form method="POST" action="{{ route('expedientes.segunda.enviar', $expediente->id) }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center px-3 py-2 rounded-md text-white text-sm font-semibold
                                    {{ $check['ok'] ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed' }}"
                                    {{ $check['ok'] ? '' : 'disabled' }}>
                                Enviar a revisión
                            </button>
                        </form>
                    </div>
                </div>

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
                </div>

                <div class="rounded-xl border border-gray-200 overflow-hidden" style="height: 80vh;">
                    <iframe src="{{ route('expedientes.segunda.pdf', $expediente->id) }}" class="w-full h-full"></iframe>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>