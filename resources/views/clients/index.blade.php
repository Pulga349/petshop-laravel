@extends('layouts.app')

@section('header')
<div class="mb-10">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black text-white uppercase tracking-tight">PetCare <span class="text-blue-500">Pro</span></h1>
            <p class="text-xs font-medium text-gray-400 uppercase tracking-[0.3em] mt-1">Premium Management - Clientes</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('clients.create') }}" 
               class="group flex items-center gap-3 bg-blue-600 hover:bg-blue-500 text-white px-8 py-4 rounded-[20px] text-sm font-black transition-all shadow-[0_8px_20px_rgba(37,99,235,0.3)] hover:-translate-y-0.5 uppercase tracking-widest">
                <i class="bi bi-plus-lg text-lg"></i>
                Añadir Cliente
            </a>
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .glass-card:hover {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
    }
    .status-badge {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        text-transform: uppercase;
    }
    .membership-tag {
        font-size: 0.6rem;
        font-weight: 800;
        padding: 0.1rem 0.4rem;
        border-radius: 4px;
    }
</style>

<div class="space-y-8">
    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Customers -->
        <div class="glass-card rounded-[2rem] p-8 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-3xl group-hover:bg-blue-500/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">TOTAL CUSTOMERS</span>
                <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-blue-400 border border-white/5">
                    <i class="bi bi-people text-lg"></i>
                </div>
            </div>
            <div class="flex items-end gap-3">
                <h3 class="text-4xl font-black text-white tracking-tighter">{{ number_format($clients->total()) }}</h3>
                <span class="mb-1.5 flex items-center text-xs font-bold text-emerald-400">
                    <i class="bi bi-arrow-up-short"></i> +12%
                </span>
            </div>
        </div>

        <!-- New This Month -->
        <div class="glass-card rounded-[2rem] p-8 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">NEW THIS MONTH</span>
                <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-emerald-400 border border-white/5">
                    <i class="bi bi-person-plus text-lg"></i>
                </div>
            </div>
            <div class="flex items-end gap-3">
                <h3 class="text-4xl font-black text-white tracking-tighter">{{ $newThisMonth }}</h3>
                <span class="mb-1.5 flex items-center text-xs font-bold text-emerald-400">
                    <i class="bi bi-arrow-up-short"></i> +{{ $newThisMonth > 0 ? round(($newThisMonth / max($clients->total(), 1)) * 100) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Top Customer -->
        <div class="glass-card rounded-[2rem] p-8 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-500/10 rounded-full blur-3xl group-hover:bg-amber-500/20 transition-all"></div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">TOP CUSTOMER</span>
                <div class="w-10 h-10 rounded-2xl bg-white/5 flex items-center justify-center text-amber-400 border border-white/5">
                    <i class="bi bi-trophy text-lg"></i>
                </div>
            </div>
            <div>
                @if($topCustomer)
                    <h3 class="text-2xl font-black text-white tracking-tight">{{ $topCustomer->name }}</h3>
                    <p class="text-sm font-medium text-gray-500 mt-1">Total spent: <span class="text-amber-400 font-bold">${{ number_format($topCustomer->sales_sum_total) }}</span></p>
                @else
                    <h3 class="text-2xl font-black text-white tracking-tight">N/A</h3>
                    <p class="text-sm font-medium text-gray-500 mt-1">No sales data yet</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Section: Customer Directory -->
    <div class="glass-card rounded-[2.5rem] overflow-hidden">
        <div class="p-8 border-b border-white/5 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-white tracking-tight">Customer Directory</h2>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-widest mt-1">Manage and monitor your premium clientele</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-xs font-bold text-gray-400 hover:text-white transition-all uppercase tracking-widest">
                    Filter
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/[0.02]">
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Customer</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Tier</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Contact</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Last Purchase</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Total Spent</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($clients as $client)
                    <tr class="group hover:bg-white/[0.02] transition-colors">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl overflow-hidden border border-white/10 shadow-xl bg-blue-600/10 flex items-center justify-center text-blue-500 font-black">
                                    @php
                                        $avatarSeed = str_replace(' ', '+', $client->name);
                                        $totalSpent = $client->sales->sum('total');
                                        $tier = match(true) {
                                            $totalSpent >= 10000 => 'Platinum',
                                            $totalSpent >= 5000  => 'Gold',
                                            $totalSpent >= 1000  => 'Silver',
                                            default              => 'Bronze',
                                        };
                                        $lastPurchase = $client->sales->max('date');
                                        $status = match(true) {
                                            !$lastPurchase || $lastPurchase < now()->subYear() => 'Lapsed',
                                            $lastPurchase >= now()->subMonths(3) => 'Active',
                                            default => 'Inactive',
                                        };
                                        $statusClass = [
                                            'Active' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            'Inactive' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                            'Lapsed' => 'bg-gray-500/10 text-gray-500 border-gray-500/20',
                                        ][$status];
                                        $tierClass = [
                                            'Platinum' => 'bg-purple-500/10 text-purple-500 border-purple-500/20',
                                            'Gold' => 'bg-amber-500/10 text-amber-500 border-amber-500/20',
                                            'Silver' => 'bg-slate-400/10 text-slate-400 border-slate-400/20',
                                            'Bronze' => 'bg-orange-700/10 text-orange-600 border-orange-700/20',
                                        ][$tier];
                                    @endphp
                                    <img src="https://i.pravatar.cc/150?u={{ $avatarSeed }}" alt="{{ $client->name }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-white">{{ $client->name }}</h4>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">ID: #C-{{ str_pad($client->id, 4, '0', STR_PAD_LEFT) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="membership-tag {{ $tierClass }} border">{{ $tier }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="space-y-0.5">
                                <p class="text-xs text-gray-300">{{ $client->email }}</p>
                                <p class="text-[10px] text-gray-500 font-medium">{{ $client->phone }}</p>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-medium text-gray-400">{{ $lastPurchase ? \Carbon\Carbon::parse($lastPurchase)->format('M d, Y') : '—' }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-sm font-black text-white">${{ number_format($totalSpent, 2) }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="status-badge {{ $statusClass }} border">{{ $status }}</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/5 text-gray-500 hover:text-white transition-all">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     class="absolute right-0 mt-2 w-48 rounded-xl bg-[#2a2b32] border border-white/10 shadow-2xl z-10 py-2 text-left">
                                    <a href="{{ route('clients.edit', $client) }}" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-300 hover:bg-white/5 hover:text-blue-400">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="flex items-center gap-3 px-4 py-2 text-sm text-red-400 hover:bg-white/5 w-full text-left" onclick="return confirm('¿Seguro?')">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-12 text-center text-gray-500 text-sm italic">
                            No hay clientes registrados aún.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="p-6 border-t border-white/5 flex items-center justify-between">
            <p class="text-xs text-gray-500 font-medium italic italic">Showing {{ $clients->count() }} of {{ $clients->total() }} premium customers</p>
            <div class="flex items-center gap-2">
                @if($clients->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-left text-xs"></i></span>
                @else
                    <a href="{{ $clients->previousPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 hover:text-white transition-all"><i class="bi bi-chevron-left text-xs"></i></a>
                @endif
                
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-500 text-white font-bold text-xs">{{ $clients->currentPage() }}</span>
                
                @if($clients->hasMorePages())
                    <a href="{{ $clients->nextPageUrl() }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-400 hover:text-white transition-all"><i class="bi bi-chevron-right text-xs"></i></a>
                @else
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white/5 text-gray-600 cursor-not-allowed"><i class="bi bi-chevron-right text-xs"></i></span>
                @endif
            </div>
        </div>
    </div>

    <!-- Bottom Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
        <!-- Purchase Trends -->
        <div class="glass-card rounded-[2.5rem] p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Purchase Trends</h3>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-widest mt-1">12 Months Volume</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-400 border border-blue-500/20">
                    <i class="bi bi-bar-chart"></i>
                </div>
            </div>
            <div class="h-64">
                <canvas id="purchaseTrendsChart"></canvas>
            </div>
        </div>

        <!-- Membership Status -->
        <div class="glass-card rounded-[2.5rem] p-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h3 class="text-xl font-bold text-white tracking-tight">Membership Status</h3>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-widest mt-1">Tier Distribution</p>
                </div>
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-400 border border-emerald-500/20">
                    <i class="bi bi-pie-chart"></i>
                </div>
            </div>
            <div class="space-y-8">
                @php
                    $tierMeta = [
                        'Platinum' => ['dot' => 'bg-purple-500', 'bar' => 'bg-purple-500', 'shadow' => 'rgba(168,85,247,0.5)'],
                        'Gold'     => ['dot' => 'bg-amber-500', 'bar' => 'bg-amber-500', 'shadow' => 'rgba(245,158,11,0.5)'],
                        'Silver'   => ['dot' => 'bg-slate-400', 'bar' => 'bg-slate-400', 'shadow' => 'rgba(148,163,184,0.5)'],
                        'Bronze'   => ['dot' => 'bg-orange-700', 'bar' => 'bg-orange-700', 'shadow' => 'rgba(194,120,3,0.5)'],
                    ];
                @endphp
                @foreach(['Platinum', 'Gold', 'Silver', 'Bronze'] as $tierName)
                @php $pct = $membershipDistribution[$tierName] ?? 0; @endphp
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full {{ $tierMeta[$tierName]['dot'] }}"></div>
                            <span class="text-xs font-bold text-white uppercase tracking-widest">{{ $tierName }} Tier</span>
                        </div>
                        <span class="text-xs font-black text-white">{{ $pct }}%</span>
                    </div>
                    <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden">
                        <div class="h-full {{ $tierMeta[$tierName]['bar'] }} rounded-full shadow-[0_0_10px_{{ $tierMeta[$tierName]['shadow'] }}]" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('purchaseTrendsChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [@foreach($chartData as $month => $total)'{{ $month }}',@endforeach],
                datasets: [{
                    label: 'Purchases',
                    data: [@foreach($chartData as $month => $total){{ $total }},@endforeach],
                    backgroundColor: function(context) {
                        const index = context.dataIndex;
                        const count = context.dataset.data.length;
                        return index === count - 1 ? '#3b82f6' : 'rgba(255, 255, 255, 0.05)';
                    },
                    borderRadius: 12,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(20, 20, 25, 0.95)',
                        padding: 12,
                        cornerRadius: 12,
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return `$${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false, drawBorder: false },
                        ticks: { color: 'rgba(255, 255, 255, 0.3)', font: { weight: 'bold', size: 10 } }
                    },
                    y: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
