<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChartDataContractTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Chart User',
            'email' => 'chart@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard/chart-data')
            ->assertRedirect(route('login'));
    }

    public function test_chart_data_returns_exactly_three_points_for_3m_range(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.chart-data') . '?range=3m');

        $response->assertOk();
        $payload = $response->json();

        $this->assertSame('3m', $payload['range']);
        $this->assertSame('Últimos 3 meses', $payload['rangeLabel']);
        $this->assertCount(3, $payload['labels']);
        $this->assertCount(3, $payload['sales']);
        $this->assertCount(3, $payload['purchases']);
    }

    public function test_chart_data_returns_single_point_for_1m_range(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.chart-data') . '?range=1m');

        $response->assertOk();
        $payload = $response->json();

        $this->assertSame('1m', $payload['range']);
        $this->assertSame('Este mes', $payload['rangeLabel']);
        $this->assertCount(1, $payload['labels']);
        $this->assertCount(1, $payload['sales']);
        $this->assertCount(1, $payload['purchases']);
    }

    public function test_chart_data_defaults_to_twelve_points_when_range_is_missing(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.chart-data'));

        $response->assertOk();
        $payload = $response->json();

        $this->assertSame('12m', $payload['range']);
        $this->assertCount(12, $payload['labels']);
        $this->assertCount(12, $payload['sales']);
        $this->assertCount(12, $payload['purchases']);
    }

    public function test_chart_data_falls_back_to_twelve_points_for_invalid_range(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.chart-data') . '?range=bogus');

        $response->assertOk();
        $payload = $response->json();

        $this->assertSame('12m', $payload['range']);
        $this->assertSame('Últimos 12 meses', $payload['rangeLabel']);
        $this->assertCount(12, $payload['labels']);
        $this->assertCount(12, $payload['sales']);
        $this->assertCount(12, $payload['purchases']);
    }

    public function test_chart_data_payload_exposes_expected_keys(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('dashboard.chart-data'));

        $response->assertOk();
        $payload = $response->json();

        $this->assertArrayHasKey('labels', $payload);
        $this->assertArrayHasKey('sales', $payload);
        $this->assertArrayHasKey('purchases', $payload);
        $this->assertArrayHasKey('range', $payload);
        $this->assertArrayHasKey('rangeLabel', $payload);
        // Chart payload only — no KPI leakage.
        $this->assertSame(
            ['range', 'rangeLabel', 'labels', 'sales', 'purchases'],
            array_keys($payload)
        );
    }
}
