<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCreateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'POS Test User',
            'email' => 'pos@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_create_renders_counter_pos_terminal(): void
    {
        $response = $this->actingAs($this->user)->get(route('pos'));

        $response->assertStatus(200);
        $response->assertSee('Confirmar y Facturar');
        $response->assertSee('Ticket de Venta');
        $response->assertSee('Más Vendidos');
        $response->assertSee('Catálogo rápido');
        $response->assertSee(route('sales.store'), false);
    }
}
