<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-white text-black font-semibold text-xs uppercase tracking-widest hover:bg-neutral-200 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
