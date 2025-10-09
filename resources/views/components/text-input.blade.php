@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge([
    'class' => '
        border-[#9F2241]
        focus:border-[#691C32]
        focus:ring-[#9F2241]
        rounded-xl
        shadow-sm
        bg-white
        text-gray-800
        placeholder-gray-400
        w-full
        transition
        duration-200
    ',
]) !!}>
