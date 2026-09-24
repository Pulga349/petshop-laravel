@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'flex items-center gap-2 border border-line bg-surface-raised px-3 py-2 font-medium text-sm text-white']) }}>
        <span class="h-2 w-2 shrink-0 bg-success" aria-hidden="true"></span>
        <span>{{ $status }}</span>
    </div>
@endif
