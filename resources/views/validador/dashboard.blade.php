<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-white leading-tight">
                    Panel del validador
                </h2>
                <p class="mt-1 text-white/70 text-sm">
                    Revisión de expedientes y control de asignaciones FASP.
                </p>
            </div>

            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-white text-xs border border-white/15">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Sesión activa
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100">
                {{-- Header interno --}}
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                        <div class="min-w-0">
                            <div class="flex items-start gap-3">
                                <div class="h-11 w-11 rounded-2xl bg-[#691C32]/10 border border-[#691C32]/15 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-[#691C32]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 6H7a2 2 0 01-2-2V4a2 2 0 012-2h7l5 5v13a2 2 0 01-2 2z" />
                                    </svg>
                                </div>

                                <div class="min-w-0">
                                    <h3 class="text-2xl sm:text-3xl font-bold text-[#691C32] leading-tight truncate">
                                        Bienvenido(a), {{ Auth::user()->nombres ?? Auth::user()->name }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500">
                                        Rol: Validador · SESESP / FASP
                                    </p>
                                </div>
                            </div>

                            <p class="mt-5 text-gray-700 leading-relaxed">
                                Desde aquí podrás revisar y dictaminar los expedientes enviados a validación, y administrar las asignaciones FASP.
                            </p>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-100">
                                    Dictaminación
                                </span>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                    Asignaciones FASP
                                </span>
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                                    Flujo controlado
                                </span>
                            </div>
                        </div>

                        {{-- Acciones rápidas --}}
                        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                            <a href="{{ route('revision.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#691C32] text-white text-sm font-semibold shadow-sm hover:bg-[#5a182b] active:scale-[.99] transition">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Expedientes en validación
                            </a>

                            <a href="{{ route('validador.fasp_asignaciones_institucion.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white text-gray-700 text-sm font-semibold border border-gray-200 shadow-sm hover:bg-gray-50 active:scale-[.99] transition">
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                Asignaciones FASP
                            </a>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Cards principales --}}
                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">

                        {{-- Bandeja de revisión --}}
                        <div class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 rounded-2xl bg-[#9F2241]/10 border border-[#9F2241]/15 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-[#9F2241]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5h6m-6 4h6m-7 4h8m-8 4h6M7 3h10a2 2 0 012 2v14a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg">Bandeja de revisión</h4>
                                            <p class="text-gray-600 text-sm mt-1">
                                                Consulta expedientes en estatus <b>En validación</b> y emite tu decisión.
                                            </p>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-800 border border-amber-100">
                                        Pendientes
                                    </span>
                                </div>

                                <div class="mt-5">
                                    <a href="{{ route('revision.index') }}"
                                       class="inline-flex items-center gap-2 text-sm font-semibold text-[#9F2241] hover:text-[#691C32] transition">
                                        Ir a expedientes en validación
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="h-1 w-full bg-gradient-to-r from-[#691C32] to-[#9F2241] opacity-0 group-hover:opacity-100 transition"></div>
                        </div>

                        {{-- Asignaciones FASP --}}
                        <div class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 rounded-2xl bg-[#9F2241]/10 border border-[#9F2241]/15 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-[#9F2241]" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 7h16M4 12h10M4 17h16" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg">Asignaciones FASP</h4>
                                            <p class="text-gray-600 text-sm mt-1">
                                                Asigna <b>Eje/Programa/Subprograma</b> a capturistas por año para controlar qué pueden capturar.
                                            </p>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        Control
                                    </span>
                                </div>

                                <div class="mt-5">
                                    <a href="{{ route('validador.fasp_asignaciones_institucion.index') }}"
                                       class="inline-flex items-center gap-2 text-sm font-semibold text-[#9F2241] hover:text-[#691C32] transition">
                                        Ir a asignaciones FASP
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="h-1 w-full bg-gradient-to-r from-[#691C32] to-[#9F2241] opacity-0 group-hover:opacity-100 transition"></div>
                        </div>

                        {{-- Notas --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex items-start gap-3">
                                        <div class="h-10 w-10 rounded-2xl bg-gray-100 border border-gray-200 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <h4 class="font-bold text-gray-900 text-lg">Notas</h4>
                                            <p class="text-gray-500 text-sm mt-1">
                                                Reglas clave del flujo de validación.
                                            </p>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                        Ayuda
                                    </span>
                                </div>

                                <ul class="mt-4 text-sm text-gray-700 space-y-2">
                                    <li class="flex gap-2">
                                        <span class="mt-2 h-1.5 w-1.5 rounded-full bg-[#691C32] flex-shrink-0"></span>
                                        <span>Si rechazas, las observaciones son obligatorias.</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="mt-2 h-1.5 w-1.5 rounded-full bg-[#691C32] flex-shrink-0"></span>
                                        <span>Al aprobar, el expediente queda bloqueado para edición.</span>
                                    </li>
                                    <li class="flex gap-2">
                                        <span class="mt-2 h-1.5 w-1.5 rounded-full bg-[#691C32] flex-shrink-0"></span>
                                        <span>Si un capturista no ve opciones, revisa sus asignaciones FASP.</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="px-6 pb-6">
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-xs text-gray-600">
                                        Tip: Mantén consistencia en las observaciones para agilizar correcciones y reenvíos.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
