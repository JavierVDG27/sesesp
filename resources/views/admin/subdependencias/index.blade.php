<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Subdependencias
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-800 border border-green-200 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow-lg rounded-2xl p-6">
                <div class="flex items-center justify-between mb-6">
                     <a href="{{ route('admin.dependencias.index') }}"
                            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                                ← Regresar a Dependencias
                     </a>
                    <div>
                        <h1 class="text-2xl font-bold text-[#691C32]">Gestión de Subdependencias</h1>
                        <p class="text-gray-600 text-sm">Administra las subdependencias por institución.</p>
                    </div>

                    <a href="{{ route('admin.subdependencias.create') }}"
                       class="px-4 py-2 rounded-lg bg-[#9F2241] text-white font-semibold shadow hover:bg-[#691C32] transition">
                        + Nueva subdependencia
                    </a>
                </div>

                <div class="overflow-x-auto bg-white rounded-xl border border-gray-200">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-[#9F2241]/10 text-[#691C32] font-semibold">
                                <th class="py-3 px-4">Nombre</th>
                                <th class="py-3 px-4">Institución</th>
                                <th class="py-3 px-4">Acciones</th>
                                <th class="py-3 px-4">Orden</th>
                            </tr>
                        </thead>

                        <tbody>
                        @forelse($subdependencias as $sub)
                        <tr class="border-t">
                            <td class="py-3 px-4 font-semibold text-gray-800">{{ $sub->nombre }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $sub->institucion?->nombre ?? '—' }}</td>

                            {{-- ORDEN (↑ ↓) --}}
                            <td class="py-3 px-4">
                            <div class="flex items-center gap-2">
                                {{-- UP --}}
                                @if($sub->prev_id)
                                <form method="POST" action="{{ route('admin.subdependencias.up', $sub) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2 py-1 rounded border border-gray-300 hover:bg-gray-50" title="Subir">↑</button>
                                </form>
                                @else
                                <button class="px-2 py-1 rounded border border-gray-200 text-gray-300 cursor-not-allowed" disabled>↑</button>
                                @endif

                                {{-- DOWN --}}
                                @if($sub->next_id)
                                <form method="POST" action="{{ route('admin.subdependencias.down', $sub) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2 py-1 rounded border border-gray-300 hover:bg-gray-50" title="Bajar">↓</button>
                                </form>
                                @else
                                <button class="px-2 py-1 rounded border border-gray-200 text-gray-300 cursor-not-allowed" disabled>↓</button>
                                @endif
                            </div>
                            </td>

                            <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.subdependencias.edit', $sub) }}" class="text-[#9F2241] hover:underline font-semibold">Editar</a>
                                <form method="POST" action="{{ route('admin.subdependencias.destroy', $sub) }}"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar esta subdependencia?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Eliminar</button>
                                </form>
                            </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 px-4 text-center text-gray-500">No hay subdependencias registradas.</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($subdependencias, 'links'))
                    <div class="mt-6">
                        {{ $subdependencias->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
