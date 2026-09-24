@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b border-accent text-sm font-medium leading-5 text-white focus:outline-none focus:border-accent transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b border-transparent text-sm font-medium leading-5 text-neutral-400 hover:text-white hover:border-line-focus focus:outline-none focus:text-white focus:border-line-focus transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
