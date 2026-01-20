{{-- resources/views/admin/users/edit.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-2xl leading-tight text-white">
                    Editar Usuario
                </h2>
                <p class="text-white/80 text-sm mt-1">
                    Actualiza los datos del usuario y guarda los cambios.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white/15 transition">
                    <i class="fas fa-arrow-left"></i>
                    Regresar
                </a>

                <a href="{{ route('admin.dashboard') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-white/20 bg-white/10 text-white hover:bg-white/15 transition">
                    <i class="fas fa-xmark"></i>
                    Cancelar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 shadow-sm">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-green-700">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="text-green-800 text-sm">
                            <p class="font-semibold">Cambios guardados</p>
                            <p class="mt-0.5">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card principal --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                {{-- Header del card --}}
                <div class="px-6 py-5 border-b border-gray-100">
                    <div class="flex items-start gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gray-50 border border-gray-200 flex items-center justify-center text-[#9F2241]">
                            <i class="fas fa-user-pen"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-semibold text-gray-800">
                                Actualizar información del usuario
                            </h3>
                            <p class="text-sm text-gray-500 mt-1 break-words">
                                Modifica los datos y guarda cambios.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-6">
                    <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        {{-- Resumen del usuario --}}
                        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-800 break-words">
                                        {{ $user->nombres }} {{ $user->apellido_paterno }} {{ $user->apellido_materno }}
                                    </p>
                                    <p class="text-sm text-gray-600 break-words mt-1 flex items-start gap-2">
                                        <i class="fas fa-envelope text-gray-400 mt-0.5"></i>
                                        <span class="break-words">{{ $user->email }}</span>
                                    </p>
                                    @if($user->curp)
                                        <p class="text-sm text-gray-600 break-words mt-1 flex items-start gap-2">
                                            <i class="fas fa-id-card text-gray-400 mt-0.5"></i>
                                            <span class="break-words uppercase">{{ $user->curp }}</span>
                                        </p>
                                    @endif
                                </div>

                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border border-gray-200 bg-white text-gray-700">
                                        <i class="fas fa-hashtag text-gray-400"></i>
                                        ID: {{ $user->id }}
                                    </span>
                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold border border-gray-200 bg-white text-gray-700">
                                        <i class="fas fa-id-badge text-gray-400"></i>
                                        {{ $user->role ? ucfirst($user->role->name) : 'Sin rol' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Sección: Datos personales --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/15">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <h4 class="font-semibold text-gray-800">Datos personales</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Nombres --}}
                                <div class="md:col-span-2">
                                    <label for="nombres" class="block text-sm font-semibold text-gray-700">
                                        Nombre(s) <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" id="nombres" name="nombres"
                                           value="{{ old('nombres', $user->nombres) }}"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:ring-[#9F2241] focus:border-[#9F2241]" />
                                    @error('nombres')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido paterno --}}
                                <div>
                                    <label for="apellido_paterno" class="block text-sm font-semibold text-gray-700">
                                        Apellido paterno <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" id="apellido_paterno" name="apellido_paterno"
                                           value="{{ old('apellido_paterno', $user->apellido_paterno) }}"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:ring-[#9F2241] focus:border-[#9F2241]" />
                                    @error('apellido_paterno')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Apellido materno --}}
                                <div>
                                    <label for="apellido_materno" class="block text-sm font-semibold text-gray-700">
                                        Apellido materno (opcional)
                                    </label>
                                    <input type="text" id="apellido_materno" name="apellido_materno"
                                           value="{{ old('apellido_materno', $user->apellido_materno) }}"
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:ring-[#9F2241] focus:border-[#9F2241]" />
                                    @error('apellido_materno')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- CURP --}}
                                <div class="md:col-span-2">
                                    <label for="curp" class="block text-sm font-semibold text-gray-700">
                                        CURP <span class="text-red-600">*</span>
                                    </label>
                                    <input type="text" id="curp" name="curp"
                                           value="{{ old('curp', $user->curp) }}"
                                           maxlength="18"
                                           style="text-transform: uppercase;"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:ring-[#9F2241] focus:border-[#9F2241]" />
                                    <p class="text-gray-500 text-xs mt-1">
                                        Verifica que la CURP corresponda al usuario, se utiliza como identificador único.
                                    </p>
                                    @error('curp')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Sección: Acceso --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/15">
                                    <i class="fas fa-lock"></i>
                                </span>
                            <h4 class="font-semibold text-gray-800">Acceso</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700">
                                        Correo electrónico <span class="text-red-600">*</span>
                                    </label>
                                    <input type="email" id="email" name="email"
                                           value="{{ old('email', $user->email) }}"
                                           required
                                           class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                  focus:ring-[#9F2241] focus:border-[#9F2241]" />
                                    @error('email')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Rol --}}
                                <div>
                                    <label for="role_id" class="block text-sm font-semibold text-gray-700">
                                        Rol <span class="text-red-600">*</span>
                                    </label>
                                    <select id="role_id" name="role_id"
                                            required
                                            class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                                   focus:ring-[#9F2241] focus:border-[#9F2241]">
                                        <option value="" disabled>Seleccione un rol</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ (int) old('role_id', $user->role_id) === (int) $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->nombre ?? $role->name ?? '') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Password (opcional) con fuerza + confirmación --}}
                            <div class="md:col-span-2" x-data="passwordHelper()">
                                <label for="password" class="block text-sm font-semibold text-gray-700">
                                    Nueva contraseña (opcional)
                                </label>

                                {{-- Input contraseña --}}
                                <div class="mt-1 relative">
                                    <input :type="showPassword ? 'text' : 'password'"
                                           x-model="password"
                                           id="password"
                                           name="password"
                                           placeholder="Dejar vacío para no cambiar"
                                           class="block w-full rounded-xl border-gray-300 shadow-sm pr-10
                                                  focus:border-[#9F2241] focus:ring-[#9F2241]" />
                                    <button type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>

                                {{-- Barra de fuerza --}}
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                        <span>Seguridad de la contraseña:</span>
                                        <span x-text="strengthLabel"></span>
                                    </div>
                                    <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-2 rounded-full transition-all duration-200"
                                             :style="{ width: strengthPercent + '%' }"
                                             :class="strengthClass"></div>
                                    </div>
                                </div>

                                <p class="text-gray-500 text-sm mt-2 flex items-start gap-2">
                                    <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                                    <span>
                                        Mínimo 8 caracteres, incluir mayúsculas, minúsculas, números y un carácter especial.
                                        Si no deseas cambiar la contraseña, deja los campos vacíos.
                                    </span>
                                </p>

                                @error('password')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror

                                {{-- Confirmación de contraseña --}}
                                <div class="mt-4">
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">
                                        Confirmar nueva contraseña
                                    </label>
                                    <div class="mt-1 relative">
                                        <input :type="showConfirm ? 'text' : 'password'"
                                               x-model="confirm"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               placeholder="Repita la nueva contraseña"
                                               class="block w-full rounded-xl border-gray-300 shadow-sm pr-10
                                                      focus:border-[#9F2241] focus:ring-[#9F2241]" />
                                        <button type="button"
                                                @click="showConfirm = !showConfirm"
                                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600">
                                            <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                        </button>
                                    </div>
                                    <p class="text-xs mt-1" x-show="password || confirm" x-text="matchText" :class="matchClass"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Institución + Subdependencia --}}
                        @php
                            $subdepsByInst = $subdependencias
                                ->groupBy('institucion_id')
                                ->map(fn($items) => $items->map(fn($s) => ['id' => $s->id, 'nombre' => $s->nombre])->values())
                                ->toArray();
                        @endphp

                        <div
                            x-data="{
                                institucionId: '{{ old('institucion_id', $user->institucion_id) }}',
                                subdependenciaId: '{{ old('subdependencia_id', $user->subdependencia_id ?? '') }}',
                                subdepsByInst: @js($subdepsByInst),
                                get subdeps() {
                                    return this.subdepsByInst[this.institucionId] ?? [];
                                }
                            }"
                            class="space-y-4"
                        >
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-xl bg-[#9F2241]/10 text-[#9F2241] border border-[#9F2241]/15">
                                    <i class="fas fa-building"></i>
                                </span>
                                <h4 class="font-semibold text-gray-800">Asignación institucional</h4>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                {{-- Institución --}}
                                <div>
                                    <label for="institucion_id" class="block text-sm font-semibold text-gray-700">
                                        Institución <span class="text-red-600">*</span>
                                    </label>

                                    <select
                                        id="institucion_id"
                                        name="institucion_id"
                                        x-model="institucionId"
                                        @change="subdependenciaId = ''"
                                        required
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                               focus:border-[#9F2241] focus:ring-[#9F2241]"
                                    >
                                        <option value="" disabled>Seleccione una institución</option>
                                        @foreach($instituciones as $inst)
                                            <option value="{{ $inst->id }}">
                                                {{ $inst->siglas ? ($inst->siglas.' - '.$inst->nombre) : $inst->nombre }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('institucion_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Subdependencia --}}
                                <div>
                                    <label for="subdependencia_id" class="block text-sm font-semibold text-gray-700">
                                        Subdependencia (opcional)
                                    </label>

                                    <select
                                        id="subdependencia_id"
                                        name="subdependencia_id"
                                        x-model="subdependenciaId"
                                        class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm
                                               focus:border-[#9F2241] focus:ring-[#9F2241] disabled:bg-gray-100"
                                        :disabled="!institucionId || subdeps.length === 0"
                                    >
                                        <option value="">Sin subdependencia</option>

                                        <template x-for="s in subdeps" :key="s.id">
                                            <option :value="s.id" x-text="s.nombre"></option>
                                        </template>
                                    </select>

                                    <p class="text-sm text-gray-500 mt-2 flex items-start gap-2">
                                        <i class="fas fa-circle-info text-gray-400 mt-0.5"></i>
                                        <span>Si no aplica, deja “Sin subdependencia”.</span>
                                    </p>

                                    @error('subdependencia_id')
                                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Botones --}}
                        <div class="pt-4 border-t border-gray-100 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-arrow-left"></i>
                                Regresar a Lista de Usuarios
                            </a>

                            <div class="flex flex-col sm:flex-row gap-2 sm:justify-end">
                                <a href="{{ route('admin.users.index') }}"
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 transition">
                                    <i class="fas fa-times"></i>
                                    Cancelar
                                </a>

                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-[#9F2241] text-white font-semibold shadow-sm hover:bg-[#691C32] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#9F2241] transition">
                                    <i class="fas fa-save"></i>
                                    Guardar cambios
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- Script para helper de contraseña --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('passwordHelper', () => ({
                password: '',
                confirm: '',
                showPassword: false,
                showConfirm: false,

                get strengthScore() {
                    const p = this.password || '';

                    if (!p.length) return 0;

                    let variations = 0;
                    if (/[a-z]/.test(p)) variations++;
                    if (/[A-Z]/.test(p)) variations++;
                    if (/[0-9]/.test(p)) variations++;
                    if (/[^A-Za-z0-9]/.test(p)) variations++;

                    let score = 0;

                    if (p.length >= 8 && variations >= 2) score = 1;
                    if (p.length >= 10 && variations >= 3) score = 2;
                    if (p.length >= 12 && variations >= 3) score = 3;

                    return score;
                },

                get strengthPercent() {
                    switch (this.strengthScore) {
                        case 1: return 33;
                        case 2: return 66;
                        case 3: return 100;
                        default: return 0;
                    }
                },

                get strengthLabel() {
                    switch (this.strengthScore) {
                        case 1: return 'Débil';
                        case 2: return 'Aceptable';
                        case 3: return 'Fuerte';
                        default: return 'Sin evaluar';
                    }
                },

                get strengthClass() {
                    switch (this.strengthScore) {
                        case 1: return 'bg-red-500';
                        case 2: return 'bg-yellow-400';
                        case 3: return 'bg-green-500';
                        default: return 'bg-gray-300';
                    }
                },

                get matchText() {
                    if (!this.password && !this.confirm) return '';
                    if (!this.confirm) return 'Escribe la confirmación de la contraseña.';
                    return this.password === this.confirm
                        ? 'Las contraseñas coinciden.'
                        : 'Las contraseñas no coinciden.';
                },

                get matchClass() {
                    if (!this.password && !this.confirm) return '';
                    if (!this.confirm) return 'text-gray-500';
                    return this.password === this.confirm
                        ? 'text-green-600'
                        : 'text-red-600';
                },
            }));
        });
    </script>

</x-app-layout>
