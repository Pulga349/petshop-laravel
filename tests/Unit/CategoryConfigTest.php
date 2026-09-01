<?php

namespace Tests\Unit;

use Tests\TestCase;

class CategoryConfigTest extends TestCase
{
    public function test_products_category_config_returns_expected_values(): void
    {
        $products = config('categories.products');
        $this->assertEquals(
            ['Nutrition', 'Accessories', 'Hygiene', 'Health', 'Other'],
            $products
        );
    }

    public function test_suppliers_category_config_returns_expected_values(): void
    {
        $suppliers = config('categories.suppliers');
        $this->assertEquals(
            ['Alimento', 'Accesorios', 'Higiene', 'Otros'],
            $suppliers
        );
    }

    public function test_styles_category_config_returns_expected_values(): void
    {
        $styles = config('categories.styles');
        $this->assertCount(4, $styles);
        $this->assertArrayHasKey('Alimento', $styles);
        $this->assertArrayHasKey('Otros', $styles);
        $this->assertStringContainsString('orange', $styles['Alimento']);
    }
}
