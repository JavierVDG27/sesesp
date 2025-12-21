<x-app-layout>

    {{-- Encabezado --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Panel del Administrador
        </h2>
    </x-slot>

    <div class="flex bg-gray-100 min-h-screen">

        {{-- SIDE MENU --}}
        <aside class="w-64 bg-[#691C32] text-white min-h-screen shadow-xl">
            <div class="p-6 text-center text-xl font-bold tracking-wide border-b border-[#9F2241]">
                Administrador
            </div>

            <nav class="mt-6 space-y-1">

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    📊 Dashboard
                </a>

                {{-- Usuarios --}}
                <a href="{{ route('admin.users.index') }}"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    👤 Gestión de Usuarios
                </a>

                {{-- Dependencias --}}
                <a href="{{ route('admin.dependencias.index') }}"
                class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    🏢 Dependencias
                </a>

                {{-- Reportes --}}
                <a href="#"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    📑 Reportes
                </a>

                {{-- Configuración --}}
                <a href="#"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    ⚙️ Configuración
                </a>

                <hr class="border-[#9F2241] my-4">

                {{-- Perfil --}}
                <a href="{{ route('profile.edit') }}"
                   class="block px-6 py-3 hover:bg-[#9F2241]/40 transition">
                    🙍 Mi Perfil
                </a>

                {{-- Logout --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full text-left px-6 py-3 hover:bg-[#9F2241]/40 transition">
                        🚪 Cerrar Sesión
                    </button>
                </form>

            </nav>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 p-10">

            <div class="bg-white shadow-lg rounded-2xl p-8">

                <h1 class="text-3xl font-bold text-[#691C32] mb-4">
                    Bienvenido, {{ Auth::user()->name }}
                </h1>

                <p class="text-gray-700 mb-8">
                    Desde este panel puedes administrar usuarios, dependencias y configuraciones del sistema.
                </p>

                {{-- Tarjetas del Dashboard --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    {{-- Usuarios --}}
                    <a href="{{ route('admin.users.index') }}"
                       class="block bg-[#9F2241]/10 hover:bg-[#9F2241]/20 p-6 rounded-xl shadow transition">
                        <h4 class="font-semibold text-[#9F2241] text-lg mb-2">Usuarios</h4>
                        <p class="text-gray-600 text-sm">Gestiona todos los usuarios registrados.</p>
                    </a>

                    {{-- Dependencias --}}
                    <div class="bg-[#9F2241]/10 hover:bg-[#9F2241]/20 p-6 rounded-xl shadow transition">
                        <h4 class="font-semibold text-[#9F2241] text-lg mb-2">Dependencias</h4>
                        <p class="text-gray-600 text-sm">Administra dependencias y asignaciones.</p>
                    </div>

                    {{-- Reportes --}}
                    <div class="bg-[#9F2241]/10 hover:bg-[#9F2241]/20 p-6 rounded-xl shadow transition">
                        <h4 class="font-semibold text-[#9F2241] text-lg mb-2">Reportes</h4>
                        <p class="text-gray-600 text-sm">Accede a estadísticas y reportes generales.</p>
                    </div>

                </div>
                    @php
                    $year = $year ?? now()->year;
                    $rows = $rows ?? collect();
                    @endphp

                {{-- Debajo de las tarjetas --}}
                    <div class="mt-10">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xl font-bold text-[#691C32]">FASP {{ $year }} (Vista/Edición)</h2>

                        <form method="GET" class="flex gap-2">
                        <input name="year" value="{{ $year }}" class="border rounded-lg px-3 py-2 w-28" />
                        <button class="bg-[#691C32] text-white px-4 py-2 rounded-lg">Ver</button>
                        </form>
                    </div>

                    <div class="overflow-x-auto bg-white rounded-2xl shadow border">
                        <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                            <th class="p-2 text-left">Nivel</th>
                            <th class="p-2 text-left">Código</th>
                            <th class="p-2 text-left">Nombre</th>

                            <th class="p-2 text-right">Fed</th>
                            <th class="p-2 text-right">Mun</th>
                            <th class="p-2 text-right">Subt Fed</th>

                            <th class="p-2 text-right">Est</th>
                            <th class="p-2 text-right">Mun</th>
                            <th class="p-2 text-right">Subt Est</th>

                            <th class="p-2 text-right">Total</th>

                            <th class="p-2 text-left">Unidad</th>
                            <th class="p-2 text-right">Cantidad</th>
                            <th class="p-2 text-left">RLCF</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($rows as $r)
                            @php
                                $indent = ($r->nivel - 1) * 16;
                                $codigo = collect([$r->eje,$r->programa,$r->subprograma,$r->capitulo,$r->concepto,$r->partida_generica,$r->bien])
                                        ->filter()->implode('.');
                            @endphp
                            <tr class="border-t {{ $r->tiene_diferencia ? 'bg-yellow-50' : '' }}">
                                <td class="p-2">{{ $r->nivel }}</td>

                                <td class="p-2">{{ $codigo }}</td>

                                <td class="p-2" style="padding-left: {{ $indent }}px;">
                                {{ $r->nombre }}
                                @if($r->tiene_diferencia)
                                    <span class="ml-2 text-xs text-yellow-700 font-semibold">DIF</span>
                                @endif
                                </td>

                                <td class="p-2 text-right">{{ number_format($r->fed_federal,2) }}</td>
                                <td class="p-2 text-right">{{ number_format($r->fed_municipal,2) }}</td>
                                <td class="p-2 text-right font-semibold">{{ number_format($r->fed_subtotal,2) }}</td>

                                <td class="p-2 text-right">{{ number_format($r->est_estatal,2) }}</td>
                                <td class="p-2 text-right">{{ number_format($r->est_municipal,2) }}</td>
                                <td class="p-2 text-right font-semibold">{{ number_format($r->est_subtotal,2) }}</td>

                                <td class="p-2 text-right font-bold">{{ number_format($r->fin_total,2) }}</td>

                                <td class="p-2">{{ $r->unidad_medida }}</td>
                                <td class="p-2 text-right">{{ $r->cantidad }}</td>
                                <td class="p-2">{{ $r->rlcf }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    </div>
            </div>

        </main>

    </div>

</x-app-layout>