<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'name' => 'Settings User',
            'email' => 'settings@test.com',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_settings_page_renders_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->user)->get(route('settings.show'));

        $response->assertStatus(200);
        $response->assertSee('Configuración');
        $response->assertSee('Impuestos');
        $response->assertSee('Descuentos por nivel de cliente');
    }

    public function test_settings_update_persists_values(): void
    {
        $response = $this->actingAs($this->user)->put(route('settings.update'), [
            'iva_rate' => '15',
            'discount_bronze' => '1',
            'discount_silver' => '6',
            'discount_gold' => '11',
            'discount_platinum' => '16',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('settings', ['key' => 'iva_rate', 'value' => '15']);
        $this->assertDatabaseHas('settings', ['key' => 'discount_bronze', 'value' => '1']);
        $this->assertDatabaseHas('settings', ['key' => 'discount_silver', 'value' => '6']);
        $this->assertDatabaseHas('settings', ['key' => 'discount_gold', 'value' => '11']);
        $this->assertDatabaseHas('settings', ['key' => 'discount_platinum', 'value' => '16']);
    }

    public function test_settings_update_rejects_iva_rate_above_100(): void
    {
        $response = $this->actingAs($this->user)->put(route('settings.update'), [
            'iva_rate' => '101',
            'discount_bronze' => '0',
            'discount_silver' => '5',
            'discount_gold' => '10',
            'discount_platinum' => '15',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('iva_rate');
        $this->assertDatabaseCount('settings', 0);
    }
}
