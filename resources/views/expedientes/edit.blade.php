{{-- resources/views/expedientes/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Edición de expediente técnico') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg rounded-2xl p-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-[#691C32]">
                            Editar expediente: {{ $expediente->folio }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">
                            Modifica la información general y el presupuesto del expediente técnico.
                        </p>
                    </div>

                    <div class="flex gap-2">
                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-200 transition">
                            ← Volver a la lista
                        </a>
                    </div>
                </div>

                {{-- Mensajes --}}
                @if (session('success'))
                    <div class="mb-4 px-4 py-3 rounded bg-green-100 text-green-800 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 px-4 py-3 rounded bg-red-100 text-red-800 text-sm">
                        <strong>Se encontraron algunos errores:</strong>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('expedientes.update', $expediente) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- ================= DATOS GENERALES ================= --}}
                    <div class="border border-gray-200 rounded-xl p-6 mb-6">
                        <h4 class="text-lg font-semibold text-[#691C32] mb-4">
                            Datos generales del expediente
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Nombre del proyecto --}}
                            <div>
                                <label for="nombre_proyecto" class="block text-sm font-medium text-gray-700">
                                    Nombre del proyecto <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="nombre_proyecto" id="nombre_proyecto"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                       focus:ring-[#691C32] focus:border-[#691C32]"
                                       value="{{ old('nombre_proyecto', $expediente->nombre_proyecto) }}" required>
                            </div>

                            {{-- Dependencia solicitante --}}
                            <div>
                                <label for="dependencia" class="block text-sm font-medium text-gray-700">
                                    Dependencia solicitante <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="dependencia" id="dependencia"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                       focus:ring-[#691C32] focus:border-[#691C32]"
                                       value="{{ old('dependencia', $expediente->dependencia) }}" required>
                            </div>

                            {{-- Tipo de recurso --}}
                            <div>
                                <label for="tipo_recurso" class="block text-sm font-medium text-gray-700">
                                    Tipo de recurso <span class="text-red-600">*</span>
                                </label>
                                @php
                                    $tipoRecurso = old('tipo_recurso', $expediente->tipo_recurso);
                                @endphp
                                <select name="tipo_recurso" id="tipo_recurso"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                        focus:ring-[#691C32] focus:border-[#691C32]" required>
                                    <option value="">Seleccione...</option>
                                    <option value="Federal" {{ $tipoRecurso == 'Federal' ? 'selected' : '' }}>Federal</option>
                                    <option value="Estatal" {{ $tipoRecurso == 'Estatal' ? 'selected' : '' }}>Estatal</option>
                                    <option value="Mixto"   {{ $tipoRecurso == 'Mixto'   ? 'selected' : '' }}>Mixto</option>
                                </select>
                            </div>

                            {{-- Año de ejercicio --}}
                            <div>
                                <label for="anio_ejercicio" class="block text-sm font-medium text-gray-700">
                                    Año de ejercicio <span class="text-red-600">*</span>
                                </label>
                                @php
                                    $anioActual = now()->year;
                                    $anios = [$anioActual, $anioActual + 1, $anioActual + 2];
                                    $anioVal = old('anio_ejercicio', $expediente->anio_ejercicio);
                                @endphp
                                <select name="anio_ejercicio" id="anio_ejercicio"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                        focus:ring-[#691C32] focus:border-[#691C32]" required>
                                    @foreach($anios as $anio)
                                        <option value="{{ $anio }}" {{ $anioVal == $anio ? 'selected' : '' }}>
                                            {{ $anio }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- ENTIDAD --}}
                            <div>
                                <label for="entidad" class="block text-sm font-medium text-gray-700">
                                    Entidad
                                </label>
                                <input type="text" name="entidad" id="entidad"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                       focus:ring-[#691C32] focus:border-[#691C32]"
                                       value="{{ old('entidad', $expediente->entidad ?? '8300') }}">
                            </div>

                            {{-- EJE --}}
                            <div>
                                <label for="eje" class="block text-sm font-medium text-gray-700">
                                    Eje
                                </label>
                                @php
                                    $ejeVal = old('eje', $expediente->eje);
                                @endphp
                                <select name="eje" id="eje"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                        focus:ring-[#691C32] focus:border-[#691C32]">
                                    <option value="">Seleccione...</option>
                                    @foreach (['01','02','03','04','05'] as $eje)
                                        <option value="{{ $eje }}" {{ $ejeVal == $eje ? 'selected' : '' }}>
                                            {{ $eje }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- PROGRAMA --}}
                            <div>
                                <label for="programa" class="block text-sm font-medium text-gray-700">
                                    Programa
                                </label>
                                @php
                                    $progVal = old('programa', $expediente->programa);
                                @endphp
                                <select name="programa" id="programa"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                        focus:ring-[#691C32] focus:border-[#691C32]">
                                    <option value="">Seleccione...</option>
                                    @foreach (range(1,14) as $num)
                                        @php $val = str_pad($num, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $val }}" {{ $progVal == $val ? 'selected' : '' }}>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- SUBPROGRAMA --}}
                            <div>
                                <label for="subprograma" class="block text-sm font-medium text-gray-700">
                                    Subprograma
                                </label>
                                @php
                                    $subVal = old('subprograma', $expediente->subprograma);
                                @endphp
                                <select name="subprograma" id="subprograma"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                        focus:ring-[#691C32] focus:border-[#691C32]">
                                    <option value="">Seleccione...</option>
                                    @foreach (range(1,33) as $num)
                                        @php $val = str_pad($num, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $val }}" {{ $subVal == $val ? 'selected' : '' }}>
                                            {{ $val }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- TEMA --}}
                            <div>
                                <label for="tema" class="block text-sm font-medium text-gray-700">
                                    Tema
                                </label>
                                <input type="text" name="tema" id="tema"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                       focus:ring-[#691C32] focus:border-[#691C32]"
                                       value="{{ old('tema', $expediente->tema) }}">
                            </div>

                            {{-- ÁREA EJECUTORA --}}
                            <div>
                                <label for="area_ejecutora" class="block text-sm font-medium text-gray-700">
                                    Área ejecutora
                                </label>
                                @php
                                    $areaVal = old('area_ejecutora', $expediente->area_ejecutora);
                                @endphp
                                <select name="area_ejecutora" id="area_ejecutora"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm text-sm
                                        focus:ring-[#691C32] focus:border-[#691C32]">
                                    <option value="">Seleccione...</option>
                                    <option value="01" {{ $areaVal == '01' ? 'selected' : '' }}>01 - Secretaría de Seguridad Pública del Estado</option>
                                    <option value="02" {{ $areaVal == '02' ? 'selected' : '' }}>02 - Fiscalía General de Justicia del Estado</option>
                                    <option value="03" {{ $areaVal == '03' ? 'selected' : '' }}>03 - Secretariado Ejecutivo del Sistema Estatal de Seguridad Pública / Consejo Estatal</option>
                                    <option value="04" {{ $areaVal == '04' ? 'selected' : '' }}>04 - Instituto Jalisciense de Ciencias Forenses</option>
                                    <option value="05" {{ $areaVal == '05' ? 'selected' : '' }}>05 - Coordinación General de Prevención del Delito</option>
                                    <option value="06" {{ $areaVal == '06' ? 'selected' : '' }}>06 - Secretaría de Gobierno</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ================= PRESUPUESTO ================= --}}
                    @php
                        $presupuestos = old('presupuestos', $expediente->presupuestos->toArray());
                    @endphp

                    <div class="border border-gray-200 rounded-xl p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-[#691C32]">
                                Presupuesto del proyecto
                            </h4>
                            <button type="button" id="btn-agregar-concepto"
                                    class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium
                                           bg-[#691C32] text-white hover:bg-[#4e1324] transition">
                                Agregar concepto
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs border border-gray-200" id="tabla-presupuesto">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 border min-w-[110px]">Capítulo</th>
                                        <th class="px-3 py-2 border min-w-[80px]">Partida genérica</th>
                                        <th class="px-3 py-2 border min-w-[300px]">Concepto</th>
                                        <th class="px-3 py-2 border min-w-[80px]">Bien</th>
                                        <th class="px-3 py-2 border min-w-[120px]">Unidad medida</th>
                                        <th class="px-3 py-2 border min-w-[80px]">Cantidad</th>
                                        <th class="px-3 py-2 border min-w-[120px]">Persona</th>
                                        <th class="px-3 py-2 border min-w-[120px]">RLC</th>
                                        <th class="px-3 py-2 border min-w-[150px] text-right">FASP Federal</th>
                                        <th class="px-3 py-2 border min-w-[150px] text-right">FASP Municipal</th>
                                        <th class="px-3 py-2 border min-w-[150px] text-right">FASP Subtotal</th>
                                        <th class="px-3 py-2 border min-w-[150px] text-right">Estatal</th>
                                        <th class="px-3 py-2 border min-w-[150px] text-right">Mun. Estatal</th>
                                        <th class="px-3 py-2 border min-w-[150px] text-right">Est. Subtotal</th>
                                        <th class="px-3 py-2 border min-w-[200px] text-right">Total financ.</th>
                                        <th class="px-2 py-2 border w-10"></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($presupuestos as $i => $p)
                                        <tr class="fila-concepto">
                                            {{-- CAPÍTULO --}}
                                            <td class="border px-2 py-1">
                                                <select name="presupuestos[{{ $i }}][capitulo]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                                                    <option value="">-</option>
                                                    @foreach (['1000','2000','3000','4000','5000','6000'] as $cap)
                                                        <option value="{{ $cap }}"
                                                            {{ ($p['capitulo'] ?? '') == $cap ? 'selected' : '' }}>
                                                            {{ $cap }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            {{-- PARTIDA GENÉRICA --}}
                                            <td class="border px-2 py-1">
                                                <input type="text" name="presupuestos[{{ $i }}][partida]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       value="{{ $p['partida'] ?? '' }}"
                                                       placeholder="Partida genérica">
                                            </td>

                                            {{-- CONCEPTO --}}
                                            <td class="border px-2 py-1">
                                                <input type="text" name="presupuestos[{{ $i }}][descripcion_concepto]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       value="{{ $p['descripcion_concepto'] ?? '' }}"
                                                       placeholder="Concepto">
                                            </td>

                                            {{-- BIEN --}}
                                            <td class="border px-2 py-1">
                                                <input type="text" name="presupuestos[{{ $i }}][bien]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       value="{{ $p['bien'] ?? '' }}"
                                                       placeholder="Bien">
                                            </td>

                                            {{-- UNIDAD DE MEDIDA --}}
                                            <td class="border px-2 py-1">
                                                @php
                                                    $unidadVal = $p['unidad'] ?? '';
                                                @endphp
                                                <select name="presupuestos[{{ $i }}][unidad]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                                                    <option value="">Seleccione...</option>
                                                    @foreach (['Servicio','Lote','Pieza','Póliza','Kit','Licencia','Paquete','Caja','Equipo','Sistema'] as $um)
                                                        <option value="{{ $um }}" {{ $unidadVal == $um ? 'selected' : '' }}>
                                                            {{ $um }}
                                                        </option>
                                                    @endforeach
                                                    <option value="otro" {{ $unidadVal == 'otro' ? 'selected' : '' }}>
                                                        Otro (especificar en concepto)
                                                    </option>
                                                </select>
                                            </td>

                                            {{-- CANTIDAD --}}
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[{{ $i }}][cantidad]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       value="{{ $p['cantidad'] ?? '' }}">
                                            </td>

                                            {{-- PERSONA --}}
                                            <td class="border px-1 py-1">
                                                <input type="text" name="presupuestos[{{ $i }}][persona]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       value="{{ $p['persona'] ?? '' }}"
                                                       placeholder="Persona / responsable">
                                            </td>

                                            {{-- RLC --}}
                                            <td class="border px-2 py-1">
                                                @php
                                                    $rlcVal = $p['rlc'] ?? '';
                                                @endphp
                                                <select name="presupuestos[{{ $i }}][rlc]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-xs input-rlc">
                                                    <option value="">-</option>
                                                    @foreach (['AE','FC','FC/FM','AEN'] as $op)
                                                        <option value="{{ $op }}" {{ $rlcVal == $op ? 'selected' : '' }}>
                                                            {{ $op }}
                                                        </option>
                                                    @endforeach
                                                    <option value="otro" {{ $rlcVal == 'otro' ? 'selected' : '' }}>Otro</option>
                                                </select>
                                            </td>

                                            {{-- FASP FEDERAL --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[{{ $i }}][fasp_federal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-fasp-federal text-right"
                                                       value="{{ $p['fasp_federal'] ?? '' }}">
                                            </td>

                                            {{-- FASP MUNICIPAL --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[{{ $i }}][fasp_municipal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-fasp-municipal text-right"
                                                       value="{{ $p['fasp_municipal'] ?? '' }}">
                                            </td>

                                            {{-- FASP SUBTOTAL (AUTO) --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0" readonly
                                                       name="presupuestos[{{ $i }}][fasp_subtotal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-fasp-subtotal text-right"
                                                       value="{{ $p['fasp_subtotal'] ?? '' }}">
                                            </td>

                                            {{-- ESTATAL --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[{{ $i }}][est_estatal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-est-estatal text-right"
                                                       value="{{ $p['est_estatal'] ?? '' }}">
                                            </td>

                                            {{-- MUNICIPAL ESTATAL --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[{{ $i }}][est_municipal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-est-municipal text-right"
                                                       value="{{ $p['est_municipal'] ?? '' }}">
                                            </td>

                                            {{-- ESTATAL SUBTOTAL (AUTO) --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0" readonly
                                                       name="presupuestos[{{ $i }}][est_subtotal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-est-subtotal text-right"
                                                       value="{{ $p['est_subtotal'] ?? '' }}">
                                            </td>

                                            {{-- TOTAL FINANCIAMIENTO (AUTO) --}}
                                            <td class="border px-2 py-1">
                                                <input type="number" step="0.01" min="0" readonly
                                                       name="presupuestos[{{ $i }}][total_financiamiento]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-total-financ text-right"
                                                       value="{{ $p['total_financiamiento'] ?? '' }}">
                                            </td>

                                            {{-- ELIMINAR FILA --}}
                                            <td class="border px-2 py-1 text-center">
                                                <button type="button"
                                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                                               bg-red-500 text-white text-xs font-bold btn-eliminar-fila">
                                                    X
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        {{-- Si no hay conceptos, mostrar una fila vacía como en create --}}
                                        <tr class="fila-concepto">
                                            <td class="border px-1 py-1">
                                                <select name="presupuestos[0][capitulo]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                                                    <option value="">-</option>
                                                    @foreach (['1000','2000','3000','4000','5000','6000'] as $cap)
                                                        <option value="{{ $cap }}">{{ $cap }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="text" name="presupuestos[0][partida]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       placeholder="Partida genérica">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="text" name="presupuestos[0][descripcion_concepto]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       placeholder="Concepto">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="text" name="presupuestos[0][bien]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       placeholder="Bien">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <select name="presupuestos[0][unidad]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                                                    <option value="">Seleccione...</option>
                                                    @foreach (['Servicio','Lote','Pieza','Póliza','Kit','Licencia','Paquete','Caja','Equipo','Sistema'] as $um)
                                                        <option value="{{ $um }}">{{ $um }}</option>
                                                    @endforeach
                                                    <option value="otro">Otro (especificar en concepto)</option>
                                                </select>
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[0][cantidad]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="text" name="presupuestos[0][persona]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                       placeholder="Persona / responsable">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <select name="presupuestos[0][rlc]"
                                                        class="w-full border-gray-300 rounded-md shadow-sm text-xs input-rlc">
                                                    <option value="">-</option>
                                                    @foreach (['AE','FC','FC/FM','AEN'] as $op)
                                                        <option value="{{ $op }}">{{ $op }}</option>
                                                    @endforeach
                                                    <option value="otro">Otro</option>
                                                </select>
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[0][fasp_federal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-fasp-federal">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[0][fasp_municipal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-fasp-municipal">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0" readonly
                                                       name="presupuestos[0][fasp_subtotal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-fasp-subtotal">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[0][est_estatal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-est-estatal">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0"
                                                       name="presupuestos[0][est_municipal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-est-municipal">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0" readonly
                                                       name="presupuestos[0][est_subtotal]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-est-subtotal">
                                            </td>
                                            <td class="border px-1 py-1">
                                                <input type="number" step="0.01" min="0" readonly
                                                       name="presupuestos[0][total_financiamiento]"
                                                       class="w-full border-gray-300 rounded-md shadow-sm text-xs input-total-financ">
                                            </td>
                                            <td class="border px-1 py-1 text-center">
                                                <button type="button"
                                                        class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                                               bg-red-500 text-white text-xs font-bold btn-eliminar-fila">
                                                    X
                                                </button>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <th colspan="14" class="px-2 py-2 border text-right text-xs font-semibold">
                                            Total financiamiento conjunto:
                                        </th>
                                        <th class="px-1 py-1 border">
                                            <input type="number" step="0.01"
                                                   class="w-full border-gray-300 rounded-md shadow-sm text-xs"
                                                   id="total_general_financiamiento" readonly>
                                        </th>
                                        <th class="border"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <p class="mt-2 text-xs text-gray-500">
                            Los subtotales de FASP y Estatales se calculan automáticamente, así como el total de financiamiento conjunto.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('expedientes.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-100 border border-transparent
                                  rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest
                                  hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 transition">
                            Cancelar
                        </a>

                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-[#691C32] border border-transparent
                                       rounded-md font-semibold text-xs text-white uppercase tracking-widest
                                       hover:bg-[#4e1324] active:bg-[#4e1324] focus:outline-none
                                       focus:ring-2 focus:ring-offset-2 focus:ring-[#691C32] transition">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT DE CÁLCULOS PARA EDIT --}}
    <script>
        if (!window.__presupuestoCreateInit) {
            window.__presupuestoCreateInit = true;

            (function() {
                let indice = 1;
                const tabla = document.getElementById('tabla-presupuesto');
                const btnAgregar = document.getElementById('btn-agregar-concepto');

                function recalcularFila(row) {
                    const faspFederal   = parseFloat(row.querySelector('.input-fasp-federal')?.value)   || 0;
                    const faspMunicipal = parseFloat(row.querySelector('.input-fasp-municipal')?.value) || 0;
                    const estEstatal    = parseFloat(row.querySelector('.input-est-estatal')?.value)    || 0;
                    const estMunicipal  = parseFloat(row.querySelector('.input-est-municipal')?.value)  || 0;

                    const faspSubtotal = faspFederal + faspMunicipal;
                    const estSubtotal  = estEstatal + estMunicipal;
                    const totalFinanc  = faspSubtotal + estSubtotal;

                    const faspSubtotalInput = row.querySelector('.input-fasp-subtotal');
                    const estSubtotalInput  = row.querySelector('.input-est-subtotal');
                    const totalFinancInput  = row.querySelector('.input-total-financ');

                    if (faspSubtotalInput) faspSubtotalInput.value = faspSubtotal.toFixed(2);
                    if (estSubtotalInput)  estSubtotalInput.value  = estSubtotal.toFixed(2);
                    if (totalFinancInput)  totalFinancInput.value  = totalFinanc.toFixed(2);

                    recalcularTotalGeneral();
                }

                function recalcularTotalGeneral() {
                    let totalGeneral = 0;

                    tabla.querySelectorAll('tbody tr.fila-concepto').forEach(row => {
                        const total = parseFloat(row.querySelector('.input-total-financ')?.value) || 0;
                        totalGeneral += total;
                    });

                    const totalGeneralInput = document.getElementById('total_general_financiamiento');
                    if (totalGeneralInput) {
                        totalGeneralInput.value = totalGeneral.toFixed(2);
                    }
                }

                function agregarEventosFila(row) {
                    const inputs = [
                        '.input-fasp-federal',
                        '.input-fasp-municipal',
                        '.input-est-estatal',
                        '.input-est-municipal',
                    ];

                    inputs.forEach(sel => {
                        const input = row.querySelector(sel);
                        if (input) {
                            input.addEventListener('input', () => recalcularFila(row));
                        }
                    });

                    const btnEliminar = row.querySelector('.btn-eliminar-fila');
                    if (btnEliminar) {
                        btnEliminar.addEventListener('click', () => {
                            row.remove();
                            recalcularTotalGeneral();
                        });
                    }

                    recalcularFila(row);
                }

                // Eventos para fila inicial
                tabla.querySelectorAll('tbody tr.fila-concepto').forEach(agregarEventosFila);

                // Agregar nueva fila
                btnAgregar.addEventListener('click', function() {
                    const tbody = tabla.querySelector('tbody');
                    const filaBase = tbody.querySelector('tr.fila-concepto');
                    const nuevaFila = filaBase.cloneNode(true);

                    // Limpiar valores
                    nuevaFila.querySelectorAll('input').forEach(input => {
                        input.value = '';
                    });
                    nuevaFila.querySelectorAll('select').forEach(select => {
                        select.selectedIndex = 0;
                    });

                    // Reindexar nombres [0]/[i] -> [indice]
                    nuevaFila.querySelectorAll('input, select').forEach(el => {
                        const name = el.getAttribute('name');
                        if (!name) return;
                        const nuevoName = name.replace(/\[\d+\]/, '[' + indice + ']');
                        el.setAttribute('name', nuevoName);
                    });

                    indice++;
                    tbody.appendChild(nuevaFila);
                    agregarEventosFila(nuevaFila);
                });

                // Total general al cargar
                recalcularTotalGeneral();
            })();
        }
    </script>

</x-app-layout>
