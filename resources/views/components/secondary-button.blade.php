<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 border border-line bg-surface-raised text-white font-semibold text-xs uppercase tracking-widest hover:bg-surface-hover disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
