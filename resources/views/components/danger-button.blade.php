<button {{ $attributes->merge([
    'type' => 'button',
    'class' => '
        inline-flex
        items-center
        px-4
        py-2
        bg-[#9F2241]
        border
        border-transparent
        rounded-md
        font-semibold
        text-xs
        text-white
        uppercase
        tracking-widest
        hover:bg-[#691C32]
        focus:bg-[#691C32]
        active:bg-[#691C32]
        focus:outline-none
        focus:ring-2
        focus:ring-offset-2
        focus:ring-[#9F2241]
        transition
    ',
]) }}>
    {{ $slot }}
</button>
