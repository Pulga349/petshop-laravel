@extends('layouts.app')

@section('header')
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-semibold text-gray-200">Detalle de Cliente: {{ $client->name }}</h2>
        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Client Info -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-400">Nombre</p>
                    <p class="text-lg text-gray-200">{{ $client->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Email</p>
                    <p class="text-lg text-gray-200">{{ $client->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Teléfono</p>
                    <p class="text-lg text-gray-200">{{ $client->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Dirección</p>
                    <p class="text-lg text-gray-200">{{ $client->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Miembro desde</p>
                    <p class="text-lg text-gray-200">{{ $client->created_at->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-400">Total Gastado</p>
                    <p class="text-lg text-emerald-400 font-semibold">${{ number_format($client->sales->sum('total'), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Sales History -->
        <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-gray-200">Historial de Ventas</h3>
            </div>
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Venta #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($client->sales as $sale)
                        <tr class="hover:bg-gray-750">
                            <td class="px-6 py-4 text-sm text-gray-200">
                                <a href="{{ route('sales.show', $sale) }}" class="text-blue-400 hover:text-blue-300">#{{ $sale->id }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-200">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-sm text-emerald-400 font-semibold">${{ number_format($sale->total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">No hay ventas registradas</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
