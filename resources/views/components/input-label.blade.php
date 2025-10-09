@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-[#691C32] text-sm mb-1']) }}>
    {{ $value ?? $slot }}
</label>
