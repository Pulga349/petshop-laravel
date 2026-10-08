<div id="toast-container"
     x-data="toastContainer()"
     x-init="init()"
     class="pointer-events-none fixed bottom-4 right-4 z-50 flex w-80 max-w-[calc(100vw-2rem)] flex-col gap-2">

    <template x-for="toast in toasts" :key="toast.id">
        <div :data-toast-id="toast.id"
             x-show="toast.visible"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0 translate-x-4"
             class="pointer-events-auto flex w-full items-start gap-3 border border-line-focus bg-surface-raised p-4"
             role="alert">

            <span class="flex h-8 w-8 shrink-0 items-center justify-center border border-line-focus"
                  :class="getIcon(toast.type)">
                <i class="bi text-base" :class="getGlyph(toast.type)"></i>
            </span>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-white" x-text="toast.title || toast.message"></p>
                <p class="mt-0.5 text-xs text-neutral-400" x-show="toast.title && toast.message" x-text="toast.message"></p>
            </div>

            <button type="button"
                    @click="remove(toast.id)"
                    class="-mr-1 -mt-1 flex h-7 w-7 shrink-0 items-center justify-center text-neutral-400 transition hover:bg-surface-hover hover:text-white"
                    aria-label="Cerrar">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
    </template>
</div>
