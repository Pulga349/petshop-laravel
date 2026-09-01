<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Models\Client;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClientController extends Controller
{
    public function index(): View
    {
        $clients = Client::with('sales')->paginate(15);

        // KPI: clients created this month
        $newThisMonth = Client::whereMonth('created_at', now()->month)->count();

        // KPI: top customer by total sales
        $topCustomer = Client::withSum('sales', 'total')
            ->whereHas('sales')
            ->orderByDesc('sales_sum_total')
            ->first();

        // KPI: monthly sales aggregation for Chart.js (last 12 months)
        $chartData = Sale::where('date', '>=', now()->subMonths(12))
            ->get(['date', 'total'])
            ->groupBy(fn ($sale) => \Carbon\Carbon::parse($sale->date)->format('Y-m'))
            ->map(fn ($group) => $group->sum('total'));

        // KPI: membership tier distribution
        $allClients = Client::with('sales')->get();
        $distribution = ['Bronze' => 0, 'Silver' => 0, 'Gold' => 0, 'Platinum' => 0];
        foreach ($allClients as $c) {
            $spent = $c->sales->sum('total');
            $tier = match (true) {
                $spent >= 10000 => 'Platinum',
                $spent >= 5000  => 'Gold',
                $spent >= 1000  => 'Silver',
                default         => 'Bronze',
            };
            $distribution[$tier]++;
        }
        $totalClients = max(count($allClients), 1); // avoid division by zero
        $membershipDistribution = array_map(fn ($count) => round(($count / $totalClients) * 100), $distribution);

        return view('clients.index', compact(
            'clients', 'newThisMonth', 'topCustomer', 'chartData', 'membershipDistribution'
        ));
    }

    public function store(StoreClientRequest $request): RedirectResponse
    {
        Client::create($request->validated());
        return redirect()->route('clients.index')->with('success', 'Cliente creado correctamente');
    }

    public function show(Client $client): View
    {
        $client->load('sales');
        return view('clients.show', compact('client'));
    }

    public function create(): View
    {
        return view('clients.create');
    }

    public function edit(Client $client): View
    {
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());
        return redirect()->route('clients.index')->with('success', 'Cliente actualizado correctamente');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado correctamente');
    }
}