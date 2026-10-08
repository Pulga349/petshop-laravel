@extends('layouts.app')

@section('header')
    <div class="page-heading">
        <div>
            <p class="text-sm font-medium text-accent">Cliente</p>
            <h2>Detalle de Cliente: {{ $client->name }}</h2>
        </div>
        <a href="{{ route('clients.index') }}" class="btn-secondary">
            Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="space-y-6">
        <!-- Client Info -->
        <div class="surface p-6">
            <div class="grid grid-cols-2 gap-6 md:grid-cols-3">
                <div>
                    <p class="text-sm text-neutral-400">Nombre</p>
                    <p class="text-lg text-white">{{ $client->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Email</p>
                    <p class="text-lg text-white">{{ $client->email ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Teléfono</p>
                    <p class="font-mono text-lg text-white">{{ $client->phone ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Dirección</p>
                    <p class="text-lg text-white">{{ $client->address ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Miembro desde</p>
                    <p class="font-mono text-lg text-white">{{ $client->created_at->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-neutral-400">Total Gastado</p>
                    <p class="font-mono text-lg font-semibold text-white">${{ number_format($client->sales->sum('total'), 2) }}</p>
                </div>
            </div>
        </div>

        <!-- Sales History -->
        <div>
            <div class="mb-3">
                <h3 class="text-lg font-semibold text-white">Historial de Ventas</h3>
            </div>
            <div class="table-shell">
                <div class="overflow-x-auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Venta #</th>
                                <th>Fecha</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($client->sales as $sale)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $sale) }}" class="font-mono text-accent hover:text-white hover:underline">#{{ $sale->id }}</a>
                                    </td>
                                    <td class="text-neutral-400">{{ \Carbon\Carbon::parse($sale->date)->format('d/m/Y') }}</td>
                                    <td class="font-mono font-semibold text-white">${{ number_format($sale->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-neutral-400">No hay ventas registradas</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
