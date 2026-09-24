@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-line bg-surface-raised text-white placeholder:text-neutral-400 focus:border-line-focus focus:outline-none']) }}>
