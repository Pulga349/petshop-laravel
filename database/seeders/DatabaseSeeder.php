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

        // 2. Categories (needed by products and suppliers)
        $this->call(CategorySeeder::class);

        // 3. Suppliers first (needed by products)
        $this->call(SupplierSeeder::class);

        // 4. Clients
        $this->call(ClientSeeder::class);

        // 5. Products (needs suppliers)
        $this->call(ProductSeeder::class);

        // 6. Purchases (needs suppliers + products)
        $this->call(PurchaseSeeder::class);

        // 7. Sales (needs clients + products)
        $this->call(SaleSeeder::class);
    }
}