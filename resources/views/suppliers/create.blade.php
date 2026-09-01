@extends('layouts.app')

@section('content')
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Blurred Background Overlay -->
    <div class="absolute inset-0 bg-[#0b0c10]/60 backdrop-blur-xl"
         onclick="window.location.href='{{ route('suppliers.index') }}'"></div>
    
    <!-- Modal Container -->
    <div class="relative w-full max-w-3xl transform transition-all">
        <!-- Subtle outer glow -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-[2.5rem] blur-2xl opacity-50"></div>
        
        <!-- Glassmorphism Modal -->
        <div class="relative bg-[#1a1c23]/40 backdrop-blur-3xl border border-white/10 rounded-[2rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)] flex flex-col max-h-[85vh]">
            
            <!-- Sticky Header with Close Button -->
            <div class="flex items-center justify-between p-8 pb-4 shrink-0">
                <div class="flex-1"></div>
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-black text-white tracking-tight uppercase">Crear Proveedor</h2>
                    <div class="h-1 w-16 bg-blue-600 mx-auto mt-2 rounded-full"></div>
                </div>
                <div class="flex-1 flex justify-end">
                    <a href="{{ route('suppliers.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all" title="Cerrar">
                        <i class="bi bi-x-lg text-lg"></i>
                    </a>
                </div>
            </div>

            <form action="{{ route('suppliers.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0">
                @csrf
                
                <!-- Scrollable Body -->
                <div class="flex-1 overflow-y-auto px-8">
                    <!-- Logo Upload Section -->
                    <div class="flex items-center gap-6 mb-6 group" x-data="{ photoPreview: null }">
                        <div class="relative">
                            <div class="w-20 h-20 rounded-2xl border-2 border-dashed border-white/20 flex items-center justify-center overflow-hidden transition-all group-hover:border-blue-500/50 bg-white/5 shadow-inner">
                                <template x-if="!photoPreview">
                                    <i class="bi bi-cloud-upload text-2xl text-gray-500"></i>
                                </template>
                                <template x-if="photoPreview">
                                    <img :src="photoPreview" class="w-full h-full object-cover">
                                </template>
                            </div>
                            <input type="file" class="hidden" x-ref="photo" name="logo" 
                                   @change="
                                        const reader = new FileReader();
                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                        reader.readAsDataURL($event.target.files[0]);
                                   ">
                        </div>
                        <div class="flex-1">
                            <button type="button" @click="$refs.photo.click()" class="text-[10px] font-black text-gray-300 hover:text-white transition-colors flex items-center gap-2 uppercase tracking-widest bg-white/5 px-3 py-1.5 rounded-lg border border-white/5">
                                <i class="bi bi-arrow-up-circle"></i>
                                Subir Logo
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Nombre Empresa</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="Nombre del Proveedor">
                        </div>

                        <!-- Contact Person -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Contacto</label>
                            <input type="text" name="contact_person" value="{{ old('contact_person') }}"
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="Juan Pérez">
                        </div>

                        <!-- Category -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Categoría</label>
                            <div class="relative">
                                <select name="category" required
                                        class="w-full bg-[#1a1c23]/60 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 appearance-none transition-all text-left">
                                    @foreach(config('categories.suppliers') as $cat)
                                    <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Teléfono</label>
                            <input type="text" name="phone" value="{{ old('phone') }}"
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="555-1234">
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="juan@vet.com">
                        </div>

                        <!-- Address -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Dirección</label>
                            <input type="text" name="address" value="{{ old('address') }}"
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="Av. Principal 123">
                        </div>
                    </div>
                </div>

                <!-- Sticky Footer -->
                <div class="flex items-center justify-between gap-4 px-8 pb-8 pt-6 border-t border-white/5 shrink-0">
                    <a href="{{ route('suppliers.index') }}" 
                       class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white text-xs font-black rounded-xl transition-all border border-white/5 uppercase tracking-widest">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] uppercase tracking-[0.2em]">
                        Guardar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Background Content (Simulated blurred dashboard) -->
<div class="opacity-20 pointer-events-none blur-md">
    <div class="p-8 space-y-8">
        <div class="flex justify-between items-end">
            <div class="space-y-2">
                <div class="h-8 w-48 bg-gray-700 rounded"></div>
                <div class="h-4 w-64 bg-gray-800 rounded"></div>
            </div>
            <div class="h-10 w-40 bg-blue-900/50 rounded-xl"></div>
        </div>
        <div class="grid grid-cols-2 gap-6">
            <div class="h-32 bg-gray-800/50 rounded-2xl"></div>
            <div class="h-32 bg-gray-800/50 rounded-2xl"></div>
        </div>
        <div class="bg-gray-800/30 rounded-3xl h-[400px]"></div>
    </div>
</div>
@endsection
