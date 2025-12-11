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
                            Aquí puedes consultar los expedientes que has creado, su estatus y continuar su captura.
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
                                    <th class="px-3 py-2 border hidden md:table-cell">Dependencia</th>
                                    <th class="px-3 py-2 border hidden md:table-cell">Tipo recurso</th>
                                    <th class="px-3 py-2 border">Año</th>
                                    <th class="px-3 py-2 border">Estatus</th>
                                    <th class="px-3 py-2 border">Creado</th>
                                    <th class="px-3 py-2 border w-32">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($expedientes as $expediente)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 border font-mono text-xs">
                                            {{ $expediente->folio }}
                                        </td>
                                        <td class="px-3 py-2 border">
                                            <div class="font-semibold text-gray-800 truncate max-w-xs">
                                                {{ $expediente->nombre_proyecto }}
                                            </div>
                                            <div class="md:hidden text-[11px] text-gray-500">
                                                {{ $expediente->dependencia }}
                                            </div>
                                        </td>
                                        <td class="px-3 py-2 border hidden md:table-cell">
                                            <span class="text-xs text-gray-700">
                                                {{ $expediente->dependencia }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 border hidden md:table-cell">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px]
                                                         bg-gray-100 text-gray-700">
                                                {{ $expediente->tipo_recurso }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 border text-center">
                                            {{ $expediente->anio_ejercicio }}
                                        </td>
                                        <td class="px-3 py-2 border">
                                            @php
                                                $estatus = strtolower($expediente->estatus);
                                                $color = match ($estatus) {
                                                    'borrador'   => 'bg-yellow-100 text-yellow-800',
                                                    'en revisión' => 'bg-blue-100 text-blue-800',
                                                    'aprobado'   => 'bg-green-100 text-green-800',
                                                    'rechazado'  => 'bg-red-100 text-red-800',
                                                    default      => 'bg-gray-100 text-gray-800',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium {{ $color }}">
                                                {{ ucfirst($expediente->estatus) }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-2 border text-[11px] text-gray-500">
                                            {{ $expediente->created_at?->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-3 py-2 border">
                                            <div class="flex flex-col gap-1">
                                                <a href="{{ route('expedientes.edit', $expediente) }}"
                                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                          bg-[#691C32] text-white text-[11px] hover:bg-[#4e1324] transition">
                                                    Editar
                                                </a>
                                                {{-- futuro: botón "Ver" si quieres una vista solo lectura --}}
                                                {{-- 
                                                <a href="{{ route('expedientes.show', $expediente) }}"
                                                   class="inline-flex items-center justify-center px-2 py-1 rounded-md
                                                          bg-gray-200 text-gray-800 text-[11px] hover:bg-gray-300 transition">
                                                    Ver
                                                </a>
                                                --}}
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
</x-app-layout>
