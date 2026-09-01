@extends('layouts.app')

@section('content')
<div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <!-- Blurred Background Overlay -->
    <div class="absolute inset-0 bg-[#0b0c10]/60 backdrop-blur-xl"
         onclick="window.location.href='{{ route('clients.index') }}'"></div>
    
    <!-- Modal Container -->
    <div class="relative w-full max-w-2xl transform transition-all">
        <!-- Subtle outer glow -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-[2rem] blur-2xl opacity-50"></div>
        
        <!-- Glassmorphism Modal -->
        <div class="relative bg-[#1a1c23]/40 backdrop-blur-3xl border border-white/10 rounded-[2rem] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.6)] flex flex-col max-h-[85vh]">
            
            <!-- Sticky Header with Close Button -->
            <div class="flex items-center justify-between p-8 pb-4 shrink-0">
                <div class="flex-1"></div>
                <div class="text-center flex-1">
                    <h2 class="text-2xl font-black text-white tracking-tight uppercase">Edit Client Profile</h2>
                    <div class="h-1 w-16 bg-blue-600 mx-auto mt-2 rounded-full"></div>
                </div>
                <div class="flex-1 flex justify-end">
                    <a href="{{ route('clients.index') }}" class="w-9 h-9 flex items-center justify-center rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all" title="Cerrar">
                        <i class="bi bi-x-lg text-lg"></i>
                    </a>
                </div>
            </div>

            <form action="{{ route('clients.update', $client) }}" method="POST" class="flex flex-col flex-1 min-h-0">
                @csrf
                @method('PUT')

                <!-- Scrollable Body -->
                <div class="flex-1 overflow-y-auto px-8">
                    <div class="space-y-4">
                        <!-- Name -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="e.g. John Doe">
                            @error('name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="john@example.com">
                            @error('email') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Phone -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                   class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2.5 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700"
                                   placeholder="+1 234 567 890">
                        </div>

                        <!-- Address -->
                        <div class="space-y-1.5">
                            <label class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] ml-1">Physical Address</label>
                            <textarea name="address" rows="2"
                                      class="w-full bg-white/5 border border-white/5 rounded-xl px-4 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500/30 transition-all placeholder:text-gray-700 resize-none">{{ old('address', $client->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Sticky Footer -->
                <div class="flex items-center justify-between gap-4 px-8 pb-8 pt-6 border-t border-white/5 shrink-0">
                    <a href="{{ route('clients.index') }}" 
                       class="px-6 py-3 bg-white/5 hover:bg-white/10 text-white text-xs font-black rounded-xl transition-all border border-white/5 uppercase tracking-widest">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="flex-1 px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white text-xs font-black rounded-xl transition-all shadow-[0_0_20px_rgba(37,99,235,0.3)] uppercase tracking-[0.2em]">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Background Content (Simulated blurred dashboard) -->
<div class="opacity-20 pointer-events-none blur-md">
    <div class="p-8 space-y-8">
        <div class="h-10 w-48 bg-gray-700 rounded-full"></div>
        <div class="grid grid-cols-3 gap-6">
            <div class="h-32 bg-gray-800 rounded-3xl"></div>
            <div class="h-32 bg-gray-800 rounded-3xl"></div>
            <div class="h-32 bg-gray-800 rounded-3xl"></div>
        </div>
        <div class="bg-gray-800 rounded-[2.5rem] h-[500px]"></div>
    </div>
</div>
@endsection
