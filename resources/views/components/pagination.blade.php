@props(['paginator'])

<div class="flex flex-col gap-3 border-t border-line p-4 text-sm sm:flex-row sm:items-center sm:justify-between">
    <p class="text-neutral-400">Mostrando <span class="font-mono font-semibold text-white">{{ $paginator->firstItem() ?? 0 }}–{{ $paginator->lastItem() ?? 0 }}</span> de <span class="font-mono font-semibold text-white">{{ $paginator->total() }}</span></p>
    <div class="flex items-center gap-2">
        @if($paginator->onFirstPage())
            <span class="icon-button cursor-not-allowed text-neutral-500" aria-hidden="true"><i class="bi bi-chevron-left"></i></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="icon-button" aria-label="Página anterior"><i class="bi bi-chevron-left"></i></a>
        @endif
        <span class="font-mono px-2 text-xs text-neutral-400">Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}</span>
        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="icon-button" aria-label="Página siguiente"><i class="bi bi-chevron-right"></i></a>
        @else
            <span class="icon-button cursor-not-allowed text-neutral-500" aria-hidden="true"><i class="bi bi-chevron-right"></i></span>
        @endif
    </div>
</div>
