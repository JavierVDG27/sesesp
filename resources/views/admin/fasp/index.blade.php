<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Catálogo FASP
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Importa, filtra, consulta y administra la estructura por niveles.
                </p>
            </div>
        </div>
    </x-slot>

    @php
        $hasFilters =
            request()->filled('eje') ||
            request()->filled('programa') ||
            request()->filled('subprograma') ||
            request()->filled('capitulo') ||
            request()->filled('concepto') ||
            request()->filled('partida_generica') ||
            request()->filled('bien');

        // paleta suave (tailwind)
        $ejePalette = [
            '01' => ['border' => 'border-blue-400',   'bg1' => 'bg-blue-50',   'bg2' => 'bg-blue-100',  'bg3' => 'bg-blue-50/50'],
            '02' => ['border' => 'border-emerald-400','bg1' => 'bg-emerald-50','bg2' => 'bg-emerald-100','bg3' => 'bg-emerald-50/50'],
            '03' => ['border' => 'border-amber-400',  'bg1' => 'bg-amber-50',  'bg2' => 'bg-amber-100', 'bg3' => 'bg-amber-50/50'],
            '04' => ['border' => 'border-violet-400', 'bg1' => 'bg-violet-50', 'bg2' => 'bg-violet-100','bg3' => 'bg-violet-50/50'],
            '05' => ['border' => 'border-teal-400',   'bg1' => 'bg-teal-50',   'bg2' => 'bg-teal-100',  'bg3' => 'bg-teal-50/50'],
        ];

        $defaultPalette = ['border' => 'border-slate-300', 'bg1' => 'bg-slate-50', 'bg2' => 'bg-slate-100', 'bg3' => 'bg-slate-50/50'];

        // según el nivel, aplicamos una “capa” distinta (suave)
        $levelTone = [
            1 => 'bg2', // EJE (un poquito más marcado)
            2 => 'bg1', // Programa
            3 => 'bg3', // Subprograma
            4 => 'bg-white',
            5 => 'bg-white',
            6 => 'bg-white',
            7 => 'bg-white',
        ];
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Success --}}
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

            {{-- Errors --}}
            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-red-700">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="text-red-800 text-sm">
                            <p class="font-semibold">Revisa los siguientes errores</p>
                            <ul class="list-disc list-inside mt-1 space-y-0.5">
                                @foreach($errors->all() as $e)
                                    <li>{{ $e }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card: Administración --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-[#9F2241]/10 border border-[#9F2241]/15 text-[#9F2241] flex items-center justify-center">
                            <i class="fas fa-table-list"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-800">Administración del catálogo</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Selecciona el año, importa el Excel y utiliza exportación o eliminación cuando aplique.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                        <div class="grid grid-cols-1 xl:grid-cols-12 gap-4 items-start">

                            {{-- Año --}}
                            <div class="xl:col-span-3">
                                <form method="GET" action="{{ route('admin.fasp.index') }}" class="h-full">
                                    <div class="min-h-[22px]">
                                        <label class="block text-sm font-semibold text-gray-700">Año</label>
                                    </div>

                                    <div class="flex items-center gap-2 mt-2">
                                        <input type="number" name="year" value="{{ $year }}"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2">

                                        <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition">
                                            <i class="fas fa-eye"></i>
                                            Ver
                                        </button>
                                    </div>

                                    {{-- “Spacer” para igualar altura con otros bloques --}}
                                    <div class="min-h-[18px] mt-2"></div>
                                </form>
                            </div>

                            {{-- Importar --}}
                            <div class="xl:col-span-5">
                                <form method="POST" action="{{ route('admin.fasp.import') }}"
                                    enctype="multipart/form-data"
                                    class="h-full">
                                    @csrf
                                    <input type="hidden" name="year" value="{{ $year }}">

                                    <div class="min-h-[22px]">
                                        <label class="block text-sm font-semibold text-gray-700">Importar Excel</label>
                                    </div>

                                    <div class="flex flex-col md:flex-row md:items-center gap-2 mt-2">
                                        <input type="file" name="archivo"
                                            class="w-full rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 bg-white">

                                        <button class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#691C32] text-white font-semibold shadow-sm hover:bg-[#9F2241] transition">
                                            <i class="fas fa-file-import"></i>
                                            Importar
                                        </button>
                                    </div>

                                    <div class="min-h-[18px] mt-2">
                                        <p class="text-xs text-gray-500 flex items-start gap-2">
                                            <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                                            <span>Se lee desde fila 77 (desglose). Encabezados/total se ignoran.</span>
                                        </p>
                                    </div>
                                </form>
                            </div>

                            {{-- Exportar --}}
                            <div class="xl:col-span-2">
                                <form method="GET" action="{{ route('admin.fasp.exportPlantilla') }}" class="h-full">
                                    <input type="hidden" name="year" value="{{ $year }}">

                                    <input type="hidden" name="eje" value="{{ request('eje') }}">
                                    <input type="hidden" name="programa" value="{{ request('programa') }}">
                                    <input type="hidden" name="subprograma" value="{{ request('subprograma') }}">
                                    <input type="hidden" name="capitulo" value="{{ request('capitulo') }}">
                                    <input type="hidden" name="concepto" value="{{ request('concepto') }}">
                                    <input type="hidden" name="partida_generica" value="{{ request('partida_generica') }}">
                                    <input type="hidden" name="bien" value="{{ request('bien') }}">

                                    <div class="min-h-[22px]">
                                        <label class="block text-sm font-semibold text-gray-700">Exportar</label>
                                    </div>

                                    <div class="mt-2">
                                        <button class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                {{ ($summary['count'] ?? 0) === 0 ? 'disabled' : '' }}>
                                            <i class="fas fa-file-excel"></i>
                                            Plantilla (XLSX)
                                        </button>
                                    </div>

                                    {{-- Spacer para igualar altura visual --}}
                                    <div class="min-h-[18px] mt-2"></div>
                                </form>
                            </div>


                            {{-- Eliminar --}}
                            <div class="xl:col-span-2"
                                x-data="{
                                    open: false,
                                    step: 1,
                                    confirmImpact: false,
                                    typedYear: '',
                                    typedWord: '',
                                    year: '{{ $year }}',
                                    canDelete() {
                                        const yearOk = (this.typedYear || '').trim() === String(this.year);
                                        const wordOk = (this.typedWord || '').trim().toUpperCase() === 'ELIMINAR';
                                        return this.confirmImpact && (yearOk || wordOk);
                                    },
                                    reset() {
                                        this.step = 1;
                                        this.confirmImpact = false;
                                        this.typedYear = '';
                                        this.typedWord = '';
                                    }
                                }"
                            >
                                <form method="POST" action="{{ route('admin.fasp.destroyByYear') }}" class="h-full">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="year" value="{{ $year }}">

                                    <div class="min-h-[22px]">
                                        <label class="block text-sm font-semibold text-gray-700">Eliminar</label>
                                    </div>

                                    <div class="mt-2">
                                        <button type="button"
                                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-semibold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                {{ ($summary['count'] ?? 0) === 0 ? 'disabled' : '' }}
                                                @click="open = true; reset()">
                                            <i class="fas fa-trash"></i>
                                            Eliminar catálogo
                                        </button>
                                    </div>
                                    <div class="min-h-[18px] mt-2"></div>

                                {{-- MODAL --}}
                                <div x-cloak x-show="open"
                                     class="fixed inset-0 z-[999] flex items-center justify-center p-4">
                                    {{-- Backdrop --}}
                                    <div class="absolute inset-0 bg-black/50"
                                         @click="open = false"
                                         aria-hidden="true"></div>

                                    {{-- Panel --}}
                                    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden"
                                         @keydown.escape.window="open = false">
                                        {{-- Header --}}
                                        <div class="px-6 py-5 border-b border-gray-100">
                                            <div class="flex items-start gap-3">
                                                <div class="h-11 w-11 rounded-2xl bg-red-50 border border-red-200 text-red-700 flex items-center justify-center">
                                                    <i class="fas fa-triangle-exclamation"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <h3 class="text-lg font-semibold text-gray-900">
                                                        Confirmación de eliminación ({{ $year }})
                                                    </h3>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        Esta acción elimina el catálogo FASP del año seleccionado <span class="font-semibold">y sus asignaciones</span>.
                                                        No se puede deshacer.
                                                    </p>
                                                </div>

                                                <button type="button"
                                                        class="ml-auto inline-flex items-center justify-center h-9 w-9 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50"
                                                        @click="open = false"
                                                        aria-label="Cerrar">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Body --}}
                                        <div class="p-6 space-y-4">
                                            {{-- Stepper --}}
                                            <div class="flex items-center gap-2 text-xs font-semibold">
                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border"
                                                     :class="step === 1 ? 'bg-[#9F2241]/10 text-[#9F2241] border-[#9F2241]/20' : 'bg-gray-50 text-gray-600 border-gray-200'">
                                                    <span class="h-5 w-5 rounded-full inline-flex items-center justify-center border"
                                                          :class="step === 1 ? 'border-[#9F2241]/40' : 'border-gray-200'">1</span>
                                                    Impacto
                                                </div>

                                                <div class="h-px flex-1 bg-gray-200"></div>

                                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border"
                                                     :class="step === 2 ? 'bg-red-50 text-red-700 border-red-200' : 'bg-gray-50 text-gray-600 border-gray-200'">
                                                    <span class="h-5 w-5 rounded-full inline-flex items-center justify-center border"
                                                          :class="step === 2 ? 'border-red-200' : 'border-gray-200'">2</span>
                                                    Confirmación final
                                                </div>
                                            </div>

                                            {{-- STEP 1 --}}
                                            <div x-show="step === 1" class="space-y-3">
                                                <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                                                    <p class="text-sm text-gray-700 font-semibold mb-2">Se eliminará:</p>
                                                    <ul class="text-sm text-gray-700 list-disc list-inside space-y-1">
                                                        <li>Todos los registros del catálogo del año <span class="font-semibold">{{ $year }}</span></li>
                                                        <li>Todas las asignaciones relacionadas</li>
                                                        <li>La estructura jerárquica (Eje → Bien)</li>
                                                    </ul>
                                                    <p class="text-xs text-gray-500 mt-3">
                                                        Recomendación: exporta una plantilla o realiza un respaldo antes de continuar.
                                                    </p>
                                                </div>

                                                <label class="flex items-start gap-3 rounded-2xl border border-gray-200 p-4 hover:bg-gray-50 cursor-pointer">
                                                    <input type="checkbox"
                                                           class="mt-1 rounded border-gray-300 text-[#9F2241] focus:ring-[#9F2241]"
                                                           x-model="confirmImpact">
                                                    <span class="text-sm text-gray-700">
                                                        Entiendo que esta acción es irreversible y afectará información crítica del sistema.
                                                    </span>
                                                </label>
                                            </div>

                                            {{-- STEP 2 --}}
                                            <div x-show="step === 2" class="space-y-3">
                                                <div class="rounded-2xl bg-red-50 border border-red-200 p-4">
                                                    <p class="text-sm text-red-800 font-semibold">
                                                        Confirmación final requerida
                                                    </p>
                                                    <p class="text-sm text-red-700 mt-1">
                                                        Para habilitar la eliminación, escribe el <span class="font-semibold">año</span> ({{ $year }})
                                                        o la palabra <span class="font-semibold">ELIMINAR</span>.
                                                    </p>
                                                </div>

                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700">Escribe el año</label>
                                                        <input type="text"
                                                               x-model="typedYear"
                                                               class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-3 py-2"
                                                               placeholder="Ej. {{ $year }}">
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-semibold text-gray-700">O escribe “ELIMINAR”</label>
                                                        <input type="text"
                                                               x-model="typedWord"
                                                               class="mt-1 w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 px-3 py-2"
                                                               placeholder="ELIMINAR">
                                                    </div>
                                                </div>

                                                <p class="text-xs text-gray-500">
                                                    El botón final se activará solo cuando la verificación sea correcta.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="px-6 py-5 border-t border-gray-100 bg-white">
                                            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                                                <button type="button"
                                                        class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition"
                                                        @click="open = false">
                                                    <i class="fas fa-ban"></i>
                                                    Cancelar
                                                </button>

                                                <div class="flex items-center gap-2">
                                                    <button type="button"
                                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 transition"
                                                            x-show="step === 2"
                                                            @click="step = 1">
                                                        <i class="fas fa-arrow-left"></i>
                                                        Volver
                                                    </button>

                                                    <button type="button"
                                                            class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                            x-show="step === 1"
                                                            :disabled="!confirmImpact"
                                                            @click="step = 2">
                                                        <i class="fas fa-arrow-right"></i>
                                                        Continuar
                                                    </button>

                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                            x-show="step === 2"
                                                            :disabled="!canDelete()"
                                                            @click="open = false">
                                                        <i class="fas fa-trash"></i>
                                                        Eliminar definitivamente
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- /MODAL --}}
                            </form>
                        </div>
                    </div>

                    {{-- Resumen --}}
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-sm">
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-gray-500 flex items-center gap-2">
                                <i class="fas fa-list-ol text-gray-400"></i>
                                Registros
                            </div>
                            <div class="mt-1 font-semibold text-gray-900">{{ $summary['count'] ?? 0 }}</div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-gray-500">Fed (Federal)</div>
                            <div class="mt-1 font-semibold text-gray-900">{{ number_format($summary['total_fed_federal'] ?? 0, 2) }}</div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-gray-500">Fed (Municipal)</div>
                            <div class="mt-1 font-semibold text-gray-900">{{ number_format($summary['total_fed_municipal'] ?? 0, 2) }}</div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-gray-500">Est (Estatal)</div>
                            <div class="mt-1 font-semibold text-gray-900">{{ number_format($summary['total_est_estatal'] ?? 0, 2) }}</div>
                        </div>

                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="text-gray-500">Est (Municipal)</div>
                            <div class="mt-1 font-semibold text-gray-900">{{ number_format($summary['total_est_municipal'] ?? 0, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card: Filtros --}}
            <form method="GET" action="{{ route('admin.fasp.index') }}" class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">
                <input type="hidden" name="year" value="{{ $year }}">

                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 text-[#9F2241] flex items-center justify-center">
                                <i class="fas fa-filter"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Filtros</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Aplica filtros jerárquicos para reducir el catálogo.
                                </p>
                            </div>
                        </div>

                        @if($hasFilters)
                            <span class="inline-flex items-center gap-2 text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/10 px-3 py-1 rounded-full">
                                <i class="fas fa-circle-check"></i>
                                Filtros activos
                            </span>
                        @endif
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-wrap gap-3 items-end">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Eje</label>
                            <select name="eje" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-28" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach(($ejes ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('eje')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Programa</label>
                            <select name="programa" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-28" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach(($programas ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('programa')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Subprograma</label>
                            <select name="subprograma" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-32" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach(($subprogramas ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('subprograma')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Capítulo</label>
                            <select name="capitulo" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-28" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach(($capitulos ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('capitulo')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Concepto</label>
                            <select name="concepto" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-28" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach(($conceptos ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('concepto')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Partida</label>
                            <select name="partida_generica" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-32" onchange="this.form.submit()">
                                <option value="">Todas</option>
                                @foreach(($partidas ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('partida_generica')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-600">Bien</label>
                            <select name="bien" class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 w-32" onchange="this.form.submit()">
                                <option value="">Todos</option>
                                @foreach(($bienes ?? collect()) as $v)
                                    <option value="{{ $v }}" @selected(request('bien')==$v)>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#691C32] text-white font-semibold hover:bg-[#9F2241] transition">
                                <i class="fas fa-check"></i>
                                Aplicar
                            </button>
                            <a href="{{ route('admin.fasp.index', ['year'=>$year]) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 hover:bg-gray-100 transition">
                                <i class="fas fa-eraser"></i>
                                Limpiar
                            </a>
                        </div>

                        @if($hasFilters)
                            <div class="text-xs text-gray-500 ml-auto flex items-center gap-2">
                                <i class="fas fa-circle-info text-gray-400"></i>
                                Mostrando resultados filtrados.
                            </div>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Card: Tabla --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden"
                 x-data="faspTree({{ $hasFilters ? 'true' : 'false' }})"
                 x-init="init()">

                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 text-[#9F2241] flex items-center justify-center">
                                <i class="fas fa-table"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Vista del catálogo</h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    Tip: puedes arrastrar dentro del área para moverte sin usar las barras de scroll.
                                </p>
                            </div>
                        </div>

                        @if($hasFilters)
                            <div class="flex flex-wrap gap-2">
                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-900 text-white font-semibold hover:bg-black transition"
                                        @click="expandAll()">
                                    <i class="fas fa-expand"></i>
                                    Expandir todo
                                </button>

                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-700 font-semibold hover:bg-gray-100 transition"
                                        @click="collapseAll()">
                                    <i class="fas fa-compress"></i>
                                    Colapsar todo
                                </button>

                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition"
                                        @click="expandToLevel(2)">
                                    <i class="fas fa-layer-group text-gray-500"></i>
                                    2 niveles
                                </button>

                                <button type="button"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition"
                                        @click="expandToLevel(3)">
                                    <i class="fas fa-layer-group text-gray-500"></i>
                                    3 niveles
                                </button>
                            </div>
                        @else
                            <span class="inline-flex items-center gap-2 text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200 px-3 py-1 rounded-full">
                                <i class="fas fa-circle-info text-gray-400"></i>
                                Sin filtros: se muestran solo Ejes (nivel 1)
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Contenedor scroll + pan con drag --}}
                <div id="faspPanArea"
                     class="max-h-[70vh] overflow-auto"
                     style="scrollbar-gutter: stable;"
                >
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 text-gray-700 sticky top-0 z-20">
                            <tr>
                                <th class="p-3 text-left">Nivel</th>

                                <th class="p-3 text-center">Eje</th>
                                <th class="p-3 text-center">Prog</th>
                                <th class="p-3 text-center">Sub</th>
                                <th class="p-3 text-center">Cap</th>
                                <th class="p-3 text-center">Conc</th>
                                <th class="p-3 text-center">Part</th>
                                <th class="p-3 text-center">Bien</th>

                                <th class="p-3 text-left">Código</th>
                                <th class="p-3 text-left">Nombre</th>

                                <th class="p-3 text-right">Fed</th>
                                <th class="p-3 text-right">Mun</th>
                                <th class="p-3 text-right">Subt Fed</th>

                                <th class="p-3 text-right">Est</th>
                                <th class="p-3 text-right">Mun</th>
                                <th class="p-3 text-right">Subt Est</th>

                                <th class="p-3 text-right">Total</th>
                                <th class="p-3 text-left">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($rows as $r)
                                @php
                                    $codigo = collect([$r->eje,$r->programa,$r->subprograma,$r->capitulo,$r->concepto,$r->partida_generica,$r->bien])
                                        ->filter(fn($v) => $v !== null && $v !== '')
                                        ->implode('.');

                                    $fedSub = (float)$r->fed_federal + (float)$r->fed_municipal;
                                    $estSub = (float)$r->est_estatal + (float)$r->est_municipal;
                                    $total  = $fedSub + $estSub;

                                    $ejeKey = $r->eje ?: '00';
                                    $pal = $ejePalette[$ejeKey] ?? $defaultPalette;

                                    $toneKey = $levelTone[$r->nivel] ?? 'bg-white';
                                    $bgClass = is_string($toneKey) ? $toneKey : ($pal[$toneKey] ?? 'bg-white');

                                    $borderClass = $r->nivel <= 3 ? ('border-l-4 ' . $pal['border']) : 'border-l-4 border-gray-200';
                                    $rowClass = $bgClass . ' ' . $borderClass;
                                @endphp

                                <tr class="hover:bg-gray-50 {{ $rowClass }}"
                                    data-row
                                    data-id="{{ $r->id }}"
                                    data-parent="{{ $r->parent_id ?? '' }}"
                                    data-nivel="{{ $r->nivel }}"
                                    x-show="visible[{{ $r->id }}] ?? false"
                                >
                                    <td class="p-3">{{ $r->nivel }}</td>

                                    <td class="p-3 text-center">{{ $r->eje }}</td>
                                    <td class="p-3 text-center">{{ $r->programa }}</td>
                                    <td class="p-3 text-center">{{ $r->subprograma }}</td>
                                    <td class="p-3 text-center">{{ $r->capitulo }}</td>
                                    <td class="p-3 text-center">{{ $r->concepto }}</td>
                                    <td class="p-3 text-center">{{ $r->partida_generica }}</td>
                                    <td class="p-3 text-center">{{ $r->bien }}</td>

                                    <td class="p-3 font-mono text-xs text-gray-700">{{ $codigo }}</td>

                                    <td class="p-3">
                                        <div class="flex items-start gap-2" style="padding-left: {{ ($r->nivel - 1) * 14 }}px;">
                                            <button type="button"
                                                    class="w-7 h-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center shadow-sm"
                                                    x-show="hasChildren({{ $r->id }}) && {{ $hasFilters ? 'true' : 'false' }}"
                                                    @click="toggle({{ $r->id }})"
                                                    :title="expanded[{{ $r->id }}] ? 'Contraer' : 'Expandir'">
                                                <span class="font-bold" x-text="expanded[{{ $r->id }}] ? '−' : '+'"></span>
                                            </button>

                                            <div class="w-7 h-7" x-show="!hasChildren({{ $r->id }})"></div>

                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-black/5 text-gray-700">
                                                        Nivel {{ $r->nivel }}
                                                    </span>

                                                    <div class="font-semibold text-gray-800 break-words">
                                                        {{ $r->nombre }}
                                                    </div>
                                                </div>

                                                @if(!$hasFilters && $r->nivel == 1)
                                                    <div class="mt-1">
                                                        <a href="{{ route('admin.fasp.index', ['year'=>$year, 'eje'=>$r->eje]) }}"
                                                           class="inline-flex items-center gap-2 text-xs font-semibold text-[#691C32] hover:underline">
                                                            Ver eje {{ $r->eje }}
                                                            <i class="fas fa-arrow-right text-[11px]"></i>
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="p-3 text-right">{{ number_format($r->fed_federal,2) }}</td>
                                    <td class="p-3 text-right">{{ number_format($r->fed_municipal,2) }}</td>
                                    <td class="p-3 text-right font-semibold">{{ number_format($fedSub,2) }}</td>

                                    <td class="p-3 text-right">{{ number_format($r->est_estatal,2) }}</td>
                                    <td class="p-3 text-right">{{ number_format($r->est_municipal,2) }}</td>
                                    <td class="p-3 text-right font-semibold">{{ number_format($estSub,2) }}</td>

                                    <td class="p-3 text-right font-bold">{{ number_format($total,2) }}</td>

                                    <td class="p-3 whitespace-nowrap align-top">
                                        <details class="inline-block">
                                            <summary class="cursor-pointer text-[#691C32] font-semibold hover:underline">
                                                <i class="fas fa-pen-to-square mr-1"></i> Editar
                                            </summary>

                                            <form method="POST"
                                                  action="{{ route('admin.fasp.update', $r) }}"
                                                  class="mt-2 grid grid-cols-2 gap-2 bg-white border border-gray-200 rounded-2xl p-4 w-[380px] shadow-sm">
                                                @csrf
                                                @method('PATCH')

                                                <label class="col-span-2 text-xs font-semibold text-gray-600">Nombre</label>
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 col-span-2"
                                                       name="nombre"
                                                       value="{{ $r->nombre }}" />

                                                <label class="text-xs font-semibold text-gray-600">Fed. Federal (FASP)</label>
                                                <label class="text-xs font-semibold text-gray-600">Fed. Municipal (FASP)</label>
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2" name="fed_federal" value="{{ $r->fed_federal }}" />
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2" name="fed_municipal" value="{{ $r->fed_municipal }}" />

                                                <label class="text-xs font-semibold text-gray-600">Est. Estatal</label>
                                                <label class="text-xs font-semibold text-gray-600">Est. Municipal</label>
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2" name="est_estatal" value="{{ $r->est_estatal }}" />
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2" name="est_municipal" value="{{ $r->est_municipal }}" />

                                                <label class="text-xs font-semibold text-gray-600">Unidad de medida</label>
                                                <label class="text-xs font-semibold text-gray-600">Cantidad</label>
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2" name="unidad_medida" value="{{ $r->unidad_medida }}" />
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2" name="cantidad" value="{{ $r->cantidad }}" />

                                                <label class="col-span-2 text-xs font-semibold text-gray-600">RLCF</label>
                                                <input class="rounded-xl border-gray-300 shadow-sm focus:border-[#9F2241] focus:ring-[#9F2241] px-3 py-2 col-span-2"
                                                       name="rlcf"
                                                       value="{{ $r->rlcf }}" />

                                                <button class="inline-flex items-center justify-center gap-2 bg-[#691C32] text-white rounded-xl px-4 py-2.5 col-span-2 mt-2 font-semibold hover:bg-[#9F2241] transition">
                                                    <i class="fas fa-save"></i>
                                                    Guardar
                                                </button>
                                            </form>
                                        </details>

                                        <details class="inline-block ml-2">
                                            <summary class="cursor-pointer text-emerald-700 font-semibold hover:underline">
                                                <i class="fas fa-plus mr-1"></i> Agregar
                                            </summary>

                                            @php
                                                $labels = [
                                                    1 => 'Programa',
                                                    2 => 'Subprograma',
                                                    3 => 'Capítulo',
                                                    4 => 'Concepto',
                                                    5 => 'Partida genérica',
                                                    6 => 'Bien',
                                                    7 => null,
                                                ];
                                                $childLabel = $labels[$r->nivel] ?? null;
                                            @endphp

                                            @if($r->nivel < 7)
                                                <form method="POST"
                                                      action="{{ route('admin.fasp.store') }}"
                                                      class="mt-2 grid grid-cols-2 gap-2 bg-white border border-gray-200 rounded-2xl p-4 w-[380px] shadow-sm">
                                                    @csrf

                                                    <input type="hidden" name="year" value="{{ $r->year }}">
                                                    <input type="hidden" name="parent_id" value="{{ $r->id }}">
                                                    <input type="hidden" name="parent_nivel" value="{{ $r->nivel }}">

                                                    <input type="hidden" name="eje" value="{{ $r->eje }}">
                                                    <input type="hidden" name="programa" value="{{ $r->programa }}">
                                                    <input type="hidden" name="subprograma" value="{{ $r->subprograma }}">
                                                    <input type="hidden" name="capitulo" value="{{ $r->capitulo }}">
                                                    <input type="hidden" name="concepto" value="{{ $r->concepto }}">
                                                    <input type="hidden" name="partida_generica" value="{{ $r->partida_generica }}">

                                                    <div class="col-span-2 text-xs text-gray-500">
                                                        Se agregará un(a) <b>{{ $childLabel }}</b> debajo de:
                                                        <span class="font-mono">{{ $codigo }}</span>
                                                    </div>

                                                    <label class="text-xs font-semibold text-gray-600 col-span-2">
                                                        Código del hijo ({{ $childLabel }})
                                                    </label>
                                                    <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2 col-span-2"
                                                           name="child_codigo"
                                                           placeholder="Solo el código del hijo. Ej: 01 | 3000 | 3600 | 361 | 0507">

                                                    <label class="text-xs font-semibold text-gray-600 col-span-2">Nombre</label>
                                                    <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2 col-span-2"
                                                           name="nombre"
                                                           placeholder="Nombre del {{ strtolower($childLabel) }}">

                                                    @if($r->nivel == 6)
                                                        <label class="text-xs font-semibold text-gray-600">Fed. Federal</label>
                                                        <label class="text-xs font-semibold text-gray-600">Fed. Municipal</label>
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2" name="fed_federal" value="0">
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2" name="fed_municipal" value="0">

                                                        <label class="text-xs font-semibold text-gray-600">Est. Estatal</label>
                                                        <label class="text-xs font-semibold text-gray-600">Est. Municipal</label>
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2" name="est_estatal" value="0">
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2" name="est_municipal" value="0">

                                                        <label class="text-xs font-semibold text-gray-600">Unidad</label>
                                                        <label class="text-xs font-semibold text-gray-600">Cantidad</label>
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2" name="unidad_medida" placeholder="Ej: Paquete">
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2" name="cantidad" placeholder="Ej: 1">

                                                        <label class="text-xs font-semibold text-gray-600 col-span-2">RLCF</label>
                                                        <input class="rounded-xl border-gray-300 shadow-sm focus:border-emerald-600 focus:ring-emerald-600 px-3 py-2 col-span-2" name="rlcf" placeholder="Opcional">
                                                    @endif

                                                    <button class="inline-flex items-center justify-center gap-2 bg-emerald-700 text-white rounded-xl px-4 py-2.5 col-span-2 mt-2 font-semibold hover:bg-emerald-800 transition">
                                                        <i class="fas fa-plus"></i>
                                                        Crear {{ $childLabel }}
                                                    </button>
                                                </form>
                                            @else
                                                <div class="mt-2 text-xs text-gray-500">Este nivel (BIEN) no admite hijos.</div>
                                            @endif
                                        </details>

                                        <form method="POST"
                                              action="{{ route('admin.fasp.destroyRow', $r->id) }}"
                                              onsubmit="return confirm('¿Quieres borrar SOLO este registro: {{ $codigo }} ?');"
                                              class="inline-block ml-2">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 text-red-700 font-semibold hover:underline">
                                                <i class="fas fa-trash"></i>
                                                Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="19" class="p-10 text-center text-gray-500">
                                        No hay datos cargados para el año {{ $year }}.
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
    function faspTree(hasFilters) {
        return {
            childrenMap: {},   // parentId => [childId...]
            visible: {},       // id => bool
            expanded: {},      // id => bool
            allIds: [],        // ids de todos los nodos

            init() {
                const rows = Array.from(document.querySelectorAll('[data-row]'));

                rows.forEach(tr => {
                    const id = parseInt(tr.dataset.id);
                    const parent = tr.dataset.parent ? parseInt(tr.dataset.parent) : null;

                    this.allIds.push(id);

                    if (!this.childrenMap[parent]) this.childrenMap[parent] = [];
                    this.childrenMap[parent].push(id);

                    this.visible[id] = false;
                    this.expanded[id] = false;
                });

                // Si hay filtros: muestra todo para que sea “plano” y fácil de leer
                if (hasFilters) {
                    this.expandAll();
                    return;
                }

                // Inicial sin filtros: mostrar solo raíces (EJES)
                (this.childrenMap[null] || []).forEach(id => {
                    this.visible[id] = true;
                });
            },

            hasChildren(id) {
                return (this.childrenMap[id] || []).length > 0;
            },

            toggle(id) {
                const isOpen = !!this.expanded[id];
                if (isOpen) {
                    this.collapseNode(id);
                } else {
                    this.expandNode(id);
                }
            },

            expandNode(id) {
                this.expanded[id] = true;
                (this.childrenMap[id] || []).forEach(childId => {
                    this.visible[childId] = true;
                });
            },

            collapseNode(id) {
                this.expanded[id] = false;
                this.hideDescendants(id);
            },

            hideDescendants(id) {
                (this.childrenMap[id] || []).forEach(childId => {
                    this.visible[childId] = false;
                    this.expanded[childId] = false;
                    this.hideDescendants(childId);
                });
            },

            expandAll() {
                this.allIds.forEach(id => this.visible[id] = true);
                this.allIds.forEach(id => this.expanded[id] = this.hasChildren(id));
            },

            collapseAll() {
                this.allIds.forEach(id => {
                    this.visible[id] = false;
                    this.expanded[id] = false;
                });

                (this.childrenMap[null] || []).forEach(id => {
                    this.visible[id] = true;
                });
            },

            expandToLevel(maxNivel) {
                const rows = Array.from(document.querySelectorAll('[data-row]'));

                this.allIds.forEach(id => {
                    this.visible[id] = false;
                    this.expanded[id] = false;
                });

                rows.forEach(tr => {
                    const id = parseInt(tr.dataset.id);
                    const nivel = parseInt(tr.dataset.nivel);

                    if (nivel <= maxNivel) {
                        this.visible[id] = true;
                        if (nivel < maxNivel && this.hasChildren(id)) {
                            this.expanded[id] = true;
                        }
                    }
                });
            }
        }
    }

    // PAN con click+drag dentro del contenedor (sin dependencias)
    document.addEventListener('DOMContentLoaded', () => {
        const el = document.getElementById('faspPanArea');
        if (!el) return;

        let isDown = false;
        let startX = 0, startY = 0;
        let scrollLeft = 0, scrollTop = 0;

        el.style.cursor = 'grab';

        el.addEventListener('mousedown', (e) => {
            // No interferir con clicks en controles
            const interactive = e.target.closest('button, a, input, select, textarea, summary, details, label');
            if (interactive) return;

            isDown = true;
            el.style.cursor = 'grabbing';
            startX = e.pageX - el.offsetLeft;
            startY = e.pageY - el.offsetTop;
            scrollLeft = el.scrollLeft;
            scrollTop = el.scrollTop;
        });

        window.addEventListener('mouseup', () => {
            if (!isDown) return;
            isDown = false;
            el.style.cursor = 'grab';
        });

        el.addEventListener('mouseleave', () => {
            if (!isDown) return;
            isDown = false;
            el.style.cursor = 'grab';
        });

        el.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - el.offsetLeft;
            const y = e.pageY - el.offsetTop;
            const walkX = (x - startX);
            const walkY = (y - startY);
            el.scrollLeft = scrollLeft - walkX;
            el.scrollTop = scrollTop - walkY;
        });
    });
    </script>

    {{-- Recomendado para evitar parpadeo en Alpine --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>
