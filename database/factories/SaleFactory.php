<?php

namespace Database\Factories;

use App\Models\Sale;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class SaleFactory extends Factory
{
    protected $model = Sale::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'date' => fake()->dateTimeBetween('-1 months', 'now'),
            'total' => 0,
        ];
    }
}