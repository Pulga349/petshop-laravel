<div id="toast-container" 
     x-data="toastContainer()"
     x-init="init()"
     class="fixed bottom-4 right-4 z-50 w-16 space-y-2">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div :data-toast-id="toast.id"
             x-show="toast.visible"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="relative flex items-center justify-center w-12 h-12 rounded-full border shadow-lg backdrop-blur-sm"
             :class="getClasses(toast.type)">
            
            <i class="bi text-xl" :class="getIcon(toast.type)"></i>
        </div>
    </template>
</div>