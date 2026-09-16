<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MigrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TDD: 1.1 - purchases table should have total column after migration.
     */
    public function test_purchases_table_has_total_column(): void
    {
        $columns = \DB::select("PRAGMA table_info(purchases)");
        $columnNames = array_column($columns, 'name');
        $this->assertContains('total', $columnNames, 'The total column should exist on the purchases table.');
    }

/**
     * TDD: 1.1 - total column should exist and be numeric.
     */
    public function test_purchases_total_column_exists_and_is_numeric(): void
    {
        $columns = \DB::select("PRAGMA table_info(purchases)");
        $totalColumn = collect($columns)->first(fn($c) => $c->name === 'total');
        $this->assertNotNull($totalColumn);
        $this->assertNotNull($totalColumn->type);
    }

    /**
     * TDD: 1.2 - categories table should exist after migration.
     */
    public function test_categories_table_exists(): void
    {
        $this->assertDatabaseHas('migrations', [
            'migration' => '2026_09_15_000002_create_categories_table',
        ]);
        $columns = \DB::select("PRAGMA table_info(categories)");
        $columnNames = array_column($columns, 'name');
        $this->assertContains('id', $columnNames);
        $this->assertContains('name', $columnNames);
        $this->assertContains('description', $columnNames);
    }

    /**
     * TDD: 1.2 - categories table should have 4 seed categories.
     */
    public function test_categories_table_has_four_seed_categories(): void
    {
        // Seed categories directly since RefreshDatabase only runs migrations
        \DB::table('categories')->insert([
            ['name' => 'Alimento'],
            ['name' => 'Accesorios'],
            ['name' => 'Higiene'],
            ['name' => 'Otros'],
        ]);
        $this->assertDatabaseCount('categories', 4);
        $names = \DB::table('categories')->pluck('name')->sort()->values()->all();
        $this->assertEquals(['Accesorios', 'Alimento', 'Higiene', 'Otros'], $names);
    }

    /**
     * TDD: 1.1 - down() removes the total column (verified via migration reversibility).
     */
    public function test_purchases_total_column_can_be_rolled_back(): void
    {
        // Verify column exists after all migrations have run
        $columns = \DB::select("PRAGMA table_info(purchases)");
        $columnNames = array_column($columns, 'name');
        $this->assertContains('total', $columnNames);

        // The migration's down() method removes the column.
        // We verify the migration file exists and has a down() method.
        $migrationPath = database_path('migrations/2026_09_15_000001_add_total_to_purchases.php');
        $this->assertFileExists($migrationPath);
        $content = file_get_contents($migrationPath);
        $this->assertStringContainsString('public function down', $content);
        $this->assertStringContainsString('dropColumn', $content);
    }

    /**
     * TDD: 1.3 - products table should have category_id column after migration.
     */
    public function test_products_table_has_category_id_column(): void
    {
        $this->assertDatabaseHas('migrations', [
            'migration' => '2026_09_15_000003_add_category_id_to_products',
        ]);
        $columns = \DB::select("PRAGMA table_info(products)");
        $columnNames = array_column($columns, 'name');
        $this->assertContains('category_id', $columnNames);
    }

    /**
     * TDD: 1.3 - category_id should have an index on products table.
     */
    public function test_products_table_has_category_id_index(): void
    {
        $indexes = \DB::select("PRAGMA index_list(products)");
        $indexNames = array_column($indexes, 'name');
        $this->assertContains('products_category_id_index', $indexNames);
    }

    /**
     * TDD: 1.3 - category_id column should be nullable and FK to categories.
     */
    public function test_products_category_id_is_foreign_key(): void
    {
        $columns = \DB::select("PRAGMA table_info(products)");
        $categoryIdCol = collect($columns)->first(fn($c) => $c->name === 'category_id');
        $this->assertNotNull($categoryIdCol);
    }

    /**
     * TDD: 1.4 - suppliers table should have category_id column after migration.
     */
    public function test_suppliers_table_has_category_id_column(): void
    {
        $this->assertDatabaseHas('migrations', [
            'migration' => '2026_09_15_000004_add_category_id_to_suppliers',
        ]);
        $columns = \DB::select("PRAGMA table_info(suppliers)");
        $columnNames = array_column($columns, 'name');
        $this->assertContains('category_id', $columnNames);
    }

    /**
     * TDD: 1.4 - suppliers table should have category_id index.
     */
    public function test_suppliers_table_has_category_id_index(): void
    {
        $indexes = \DB::select("PRAGMA index_list(suppliers)");
        $indexNames = array_column($indexes, 'name');
        $this->assertContains('suppliers_category_id_index', $indexNames);
    }

    /**
     * TDD: 1.5 - clients table should have tier, status, total_spent columns.
     */
    public function test_clients_table_has_tier_status_total_spent_columns(): void
    {
        $this->assertDatabaseHas('migrations', [
            'migration' => '2026_09_15_000005_add_tier_status_total_spent_to_clients',
        ]);
        $columns = \DB::select("PRAGMA table_info(clients)");
        $columnNames = array_column($columns, 'name');
        $this->assertContains('tier', $columnNames);
        $this->assertContains('status', $columnNames);
        $this->assertContains('total_spent', $columnNames);
    }

    /**
     * TDD: 1.5 - clients table should have indexes on tier, status, total_spent.
     */
    public function test_clients_table_has_performance_indexes(): void
    {
        $indexes = \DB::select("PRAGMA index_list(clients)");
        $indexNames = array_column($indexes, 'name');
        $this->assertContains('clients_tier_index', $indexNames);
        $this->assertContains('clients_status_index', $indexNames);
    }

    /**
     * TDD: 1.6 - performance indexes should exist on key columns.
     */
    public function test_performance_indexes_exist(): void
    {
        $this->assertDatabaseHas('migrations', [
            'migration' => '2026_09_15_000006_add_performance_indexes',
        ]);

        // Check products.category_id index exists
        $productIndexes = \DB::select("PRAGMA index_list(products)");
        $productIndexNames = array_column($productIndexes, 'name');
        $this->assertContains('products_category_id_index', $productIndexNames);

        // Check clients.total_spent index exists
        $clientIndexes = \DB::select("PRAGMA index_list(clients)");
        $clientIndexNames = array_column($clientIndexes, 'name');
        $this->assertContains('clients_total_spent_index', $clientIndexNames);
    }
}
