@props(['name', 'maxWidth' => null])

<div
    x-data="{ show: false }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="show = false"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    id="{{ $name }}"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <div
        x-show="show"
        x-transition.opacity
        class="fixed inset-0 bg-[#691C32] bg-opacity-70"
    ></div>

    <div
        x-show="show"
        x-transition
        class="mb-6 bg-white rounded-2xl shadow-xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidth ?? 'sm:max-w-lg' }}"
    >
        @isset($title)
            <div class="flex justify-between items-center bg-[#9F2241] text-white px-6 py-3 rounded-t-2xl">
                <div class="text-lg font-semibold">{{ $title }}</div>
                <button type="button" x-on:click="show = false" class="text-white hover:text-gray-200 transition">✕</button>
            </div>
        @endisset

        <div class="p-6 text-gray-700">
            {{ $slot }}
        </div>

        @isset($footer)
            <div class="flex justify-end gap-3 px-6 pb-6">
                {{ $footer }}
            </div>
        @endisset
    </div>
</div>
