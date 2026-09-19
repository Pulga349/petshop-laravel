<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Alimento', 'description' => 'Alimentos para mascotas'],
            ['name' => 'Accesorios', 'description' => 'Accesorios y collares'],
            ['name' => 'Higiene', 'description' => 'Productos de higiene'],
            ['name' => 'Otros', 'description' => 'Otros productos'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->updateOrInsert(
                ['name' => $cat['name']],
                $cat
            );
        }
    }
}
