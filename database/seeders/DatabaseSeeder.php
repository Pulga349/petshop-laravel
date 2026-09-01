<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin user
        User::factory()->create([
            'name' => 'martin m',
            'email' => 'admin@petshop.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Suppliers first (needed by products)
        $this->call(SupplierSeeder::class);

        // 3. Clients
        $this->call(ClientSeeder::class);

        // 4. Products (needs suppliers)
        $this->call(ProductSeeder::class);

        // 5. Purchases (needs suppliers + products)
        $this->call(PurchaseSeeder::class);

        // 6. Sales (needs clients + products)
        $this->call(SaleSeeder::class);
    }
}