<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'clienttest@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_index_displays_real_kpis(): void
    {
        // Create clientA with sales totaling 6000 (Gold tier)
        $clientA = Client::create([
            'name' => 'Alpha Client',
            'email' => 'alpha@test.com',
            'phone' => '1111111111',
            'address' => 'Alpha Address',
            'created_at' => now(),
        ]);

        Sale::create(['client_id' => $clientA->id, 'date' => now()->subDays(30)->toDateString(), 'total' => 3000]);
        Sale::create(['client_id' => $clientA->id, 'date' => now()->subDays(10)->toDateString(), 'total' => 3000]);

        // Create clientB with sales totaling 500 (Bronze tier)
        $clientB = Client::create([
            'name' => 'Beta Client',
            'email' => 'beta@test.com',
            'phone' => '2222222222',
            'address' => 'Beta Address',
            'created_at' => now()->subMonths(2),
        ]);

        Sale::create(['client_id' => $clientB->id, 'date' => now()->subDays(60)->toDateString(), 'total' => 500]);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);

        // Top customer should be Alpha Client (total 6000 > 500)
        $response->assertSee('Alpha Client');

        // Alpha Client should show Gold tier (6000 is between 5000-9999)
        $response->assertSee('Gold');

        // Beta Client should show Bronze tier (500 < 1000)
        $response->assertSee('Bronze');

        // Alpha Client sales were 30 and 10 days ago — should be Active
        $response->assertSee('Active');
    }

    public function test_index_shows_na_when_no_sales(): void
    {
        $client = Client::create([
            'name' => 'No Sales Client',
            'email' => 'nosales@test.com',
            'phone' => '3333333333',
            'address' => 'No Sales Address',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertSee('N/A');
    }

    public function test_index_displays_real_total_clients_count(): void
    {
        Client::create(['name' => 'C1', 'email' => 'c1@t.com', 'phone' => '1', 'address' => 'A', 'created_at' => now()]);
        Client::create(['name' => 'C2', 'email' => 'c2@t.com', 'phone' => '2', 'address' => 'B', 'created_at' => now()]);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);
        $response->assertSee('2');
    }

    public function test_index_shows_lapsed_status_for_no_purchases(): void
    {
        Client::create([
            'name' => 'Lapsed Client',
            'email' => 'lapsed@test.com',
            'phone' => '4444444444',
            'address' => 'Lapsed Address',
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->user)->get(route('clients.index'));

        $response->assertStatus(200);

        // Client with no sales should show "Lapsed" status
        $response->assertSee('Lapsed');
    }
}
