<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl text-white leading-tight">
                    Instituciones
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Reordena, edita y administra el catálogo institucional.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.dependencias.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white/15 transition">
                    <i class="fas fa-arrow-left"></i>
                    Regresar
                </a>

                <a href="{{ route('admin.instituciones.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-[#9F2241] font-semibold shadow-sm hover:bg-gray-50 transition">
                    <i class="fas fa-plus"></i>
                    Nueva institución
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            @if(session('error'))
                <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-red-700">
                            <i class="fas fa-triangle-exclamation"></i>
                        </div>
                        <div class="text-red-800 text-sm">
                            <p class="font-semibold">No se pudo completar</p>
                            <p class="mt-0.5">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card principal --}}
            <div class="bg-white shadow-sm rounded-2xl border border-gray-200 overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-[#9F2241]">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <div>
                                <h1 class="text-lg font-semibold text-gray-800">Gestión de Instituciones</h1>
                                <p class="text-sm text-gray-500 mt-1">
                                    Usa ↑/↓ para acomodar y después guarda el orden.
                                </p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/10">
                                <i class="fas fa-sort"></i>
                                Orden por prioridad
                            </span>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 text-gray-700 border border-gray-200">
                                <i class="fas fa-circle-info text-gray-400"></i>
                                Arrastra con ↑/↓
                            </span>
                        </div>
                    </div>
                </div>

                {{-- FORM BATCH (NO anidar forms dentro del tbody) --}}
                <form method="POST" action="{{ route('admin.instituciones.orden.batch') }}" id="ordenForm">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50">
                                <tr class="text-gray-700 font-semibold">
                                    <th class="py-3 px-4 w-44">Orden</th>
                                    <th class="py-3 px-4">Nombre</th>
                                    <th class="py-3 px-4">Siglas</th>
                                    <th class="py-3 px-4">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="instTbody" class="divide-y divide-gray-100">
                                @forelse($instituciones as $inst)
                                    <tr class="hover:bg-gray-50 transition" data-id="{{ $inst->id }}">
                                        {{-- REORDENAR (↑ ↓) + ids[] --}}
                                        <td class="py-3 px-4">
                                            <div class="flex items-center gap-2">
                                                <button type="button"
                                                        class="move-up inline-flex items-center justify-center h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 shadow-sm transition"
                                                        title="Subir"
                                                        aria-label="Subir">
                                                    <i class="fas fa-arrow-up text-gray-700"></i>
                                                </button>

                                                <button type="button"
                                                        class="move-down inline-flex items-center justify-center h-9 w-9 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 shadow-sm transition"
                                                        title="Bajar"
                                                        aria-label="Bajar">
                                                    <i class="fas fa-arrow-down text-gray-700"></i>
                                                </button>

                                                <span class="order-badge inline-flex items-center gap-2 text-xs font-semibold px-3 py-1 rounded-full bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/10">
                                                    <i class="fas fa-hashtag text-[11px]"></i>
                                                    #{{ $inst->orden }}
                                                </span>
                                            </div>

                                            <input type="hidden" name="ids[]" value="{{ $inst->id }}">
                                        </td>

                                        <td class="py-3 px-4">
                                            <div class="font-semibold text-gray-800 break-words">
                                                {{ $inst->nombre }}
                                            </div>
                                        </td>

                                        <td class="py-3 px-4 text-gray-700">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-50 border border-gray-200">
                                                {{ $inst->siglas ?? '—' }}
                                            </span>
                                        </td>

                                        <td class="py-3 px-4">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <a href="{{ route('admin.instituciones.edit', $inst) }}"
                                                   class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                                                    <i class="fas fa-pen-to-square text-[#9F2241]"></i>
                                                    Editar
                                                </a>

                                                {{-- Eliminar --}}
                                                <button type="button"
                                                        class="js-open-delete-modal inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm font-semibold hover:bg-red-100 transition"
                                                        data-id="{{ $inst->id }}"
                                                        data-nombre="{{ $inst->nombre }}"
                                                        data-siglas="{{ $inst->siglas ?? '—' }}">
                                                    <i class="fas fa-trash"></i>
                                                    Eliminar
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-10 px-4 text-center text-gray-500">
                                            No hay instituciones registradas.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-5 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-gray-500 flex items-start gap-2">
                            <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                            <span>Usa ↑/↓ para acomodar y luego guarda el orden.</span>
                        </p>

                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#9F2241] text-white font-semibold shadow-sm hover:bg-[#691C32] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9F2241] transition">
                            <i class="fas fa-save"></i>
                            Guardar orden
                        </button>
                    </div>
                </form>

                {{-- Paginación (si aplica) --}}
                @if(method_exists($instituciones, 'links'))
                    <div class="px-6 py-6 border-t border-gray-100">
                        {{ $instituciones->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- FORMS OCULTOS PARA ELIMINAR (FUERA del form batch) --}}
    @foreach($instituciones as $inst)
        <form id="del-inst-{{ $inst->id }}"
              method="POST"
              action="{{ route('admin.instituciones.destroy', $inst) }}"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endforeach


    {{-- MODAL ELIMINAR --}}
    <div id="deleteModal"
        class="fixed inset-0 z-50 hidden"
        aria-labelledby="deleteModalTitle"
        role="dialog"
        aria-modal="true">

        {{-- Overlay --}}
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-[2px]" data-close></div>

        {{-- Panel --}}
        <div class="relative min-h-full flex items-center justify-center p-4">
            <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl border border-gray-200 overflow-hidden"
                id="deleteModalPanel">

                <div class="px-6 py-5 border-b border-gray-100 flex items-start gap-3">
                    <div class="h-10 w-10 rounded-xl bg-red-50 border border-red-200 flex items-center justify-center text-red-700">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex-1">
                        <h3 id="deleteModalTitle" class="text-lg font-semibold text-gray-800">
                            Confirmar eliminación
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Esta acción no se puede deshacer.
                        </p>
                    </div>

                    <button type="button"
                            class="h-9 w-9 inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition"
                            aria-label="Cerrar"
                            data-close>
                        <i class="fas fa-xmark text-gray-600"></i>
                    </button>
                </div>

                <div class="px-6 py-5">
                    <p class="text-sm text-gray-600">
                        ¿Seguro que deseas eliminar la institución:
                    </p>

                    <div class="mt-3 rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <div class="text-sm text-gray-500">Institución</div>
                        <div class="mt-1 text-base font-semibold text-gray-800 break-words" id="deleteInstNombre">—</div>

                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-white border border-gray-200 text-gray-700">
                                <i class="fas fa-id-card text-gray-400"></i>
                                <span id="deleteInstSiglas">—</span>
                            </span>

                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-red-50 border border-red-200 text-red-700">
                                <i class="fas fa-trash"></i>
                                Eliminación permanente
                            </span>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-5 border-t border-gray-100 flex flex-col sm:flex-row gap-2 sm:justify-end">
                    <button type="button"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold hover:bg-gray-50 transition"
                            data-close>
                        <i class="fas fa-ban"></i>
                        Cancelar
                    </button>

                    <button type="button"
                            id="confirmDeleteBtn"
                            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 text-white font-semibold shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 transition">
                        <i class="fas fa-trash"></i>
                        Sí, eliminar
                    </button>
                </div>

            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // =========================
  // REORDENAR
  // =========================
  const tbody = document.getElementById('instTbody');

  function renumerarBadges() {
    const rows = [...tbody.querySelectorAll('tr[data-id]')];
    rows.forEach((row, idx) => {
      const badge = row.querySelector('.order-badge');
      if (badge) badge.textContent = `#${idx + 1}`;
    });
  }
  renumerarBadges();

  tbody.addEventListener('click', (e) => {
    const up = e.target.closest('.move-up');
    const down = e.target.closest('.move-down');
    if (!up && !down) return;

    const row = e.target.closest('tr');
    if (!row) return;

    if (up) {
      const prev = row.previousElementSibling;
      if (prev) tbody.insertBefore(row, prev);
    }

    if (down) {
      const next = row.nextElementSibling;
      if (next) tbody.insertBefore(next, row);
    }

    renumerarBadges();
  });

  // =========================
  // MODAL ELIMINAR
  // =========================
  const modal = document.getElementById('deleteModal');
  const panel = document.getElementById('deleteModalPanel');
  const nombreEl = document.getElementById('deleteInstNombre');
  const siglasEl = document.getElementById('deleteInstSiglas');
  const confirmBtn = document.getElementById('confirmDeleteBtn');

  let currentDeleteId = null;
  let lastFocusedEl = null;

  function openModal({ id, nombre, siglas }) {
    currentDeleteId = id;
    lastFocusedEl = document.activeElement;

    nombreEl.textContent = nombre || '—';
    siglasEl.textContent = siglas || '—';

    modal.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden');

    setTimeout(() => confirmBtn.focus(), 0);
  }

  function closeModal() {
    modal.classList.add('hidden');
    document.documentElement.classList.remove('overflow-hidden');
    currentDeleteId = null;

    if (lastFocusedEl && typeof lastFocusedEl.focus === 'function') {
      lastFocusedEl.focus();
    }
  }

  // Abrir modal
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.js-open-delete-modal');
    if (!btn) return;

    openModal({
      id: btn.dataset.id,
      nombre: btn.dataset.nombre,
      siglas: btn.dataset.siglas
    });
  });

  modal.addEventListener('click', (e) => {
    if (e.target.closest('[data-close]')) {
      closeModal();
      return;
    }

    if (!panel.contains(e.target)) {
      closeModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (modal.classList.contains('hidden')) return;
    if (e.key === 'Escape') closeModal();
  });

  // Confirmar eliminación
  confirmBtn.addEventListener('click', () => {
    if (!currentDeleteId) return;
    const form = document.getElementById(`del-inst-${currentDeleteId}`);
    if (form) form.submit();
  });
});
</script>

</x-app-layout>