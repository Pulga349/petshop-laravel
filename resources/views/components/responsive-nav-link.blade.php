@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l border-accent text-start text-base font-medium text-white bg-surface-hover focus:outline-none focus:text-white focus:bg-surface-hover focus:border-accent transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l border-transparent text-start text-base font-medium text-neutral-400 hover:text-white hover:bg-surface-hover hover:border-line-focus focus:outline-none focus:text-white focus:bg-surface-hover focus:border-line-focus transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
