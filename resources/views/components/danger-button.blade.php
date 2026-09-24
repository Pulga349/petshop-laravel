<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 border border-danger bg-danger/10 text-white font-semibold text-xs uppercase tracking-widest hover:border-accent transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
