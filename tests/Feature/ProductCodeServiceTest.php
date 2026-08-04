<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductCodeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductCodeService();
    }

    public function test_generates_first_code_for_category_with_3_letter_prefix(): void
    {
        $cat = Category::create(['name' => 'Elektronik', 'code' => 'ELK']);
        $code = $this->service->generate($cat);

        $this->assertEquals('ELK-000001', $code);
    }

    public function test_generates_first_code_for_category_with_2_letter_prefix(): void
    {
        $cat = Category::create(['name' => 'Kart', 'code' => 'K']);
        $code = $this->service->generate($cat);

        $this->assertEquals('K-000001', $code);
    }

    public function test_generates_first_code_for_category_with_5_letter_prefix(): void
    {
        $cat = Category::create(['name' => 'Donanım', 'code' => 'DONAN']);
        $code = $this->service->generate($cat);

        $this->assertEquals('DONAN-000001', $code);
    }

    public function test_sequential_code_generation_increments_number(): void
    {
        $cat = Category::create(['name' => 'Mekanik', 'code' => 'MEK']);

        Product::create([
            'product_code'  => 'MEK-000001',
            'name'          => 'Cıvata M4',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 100,
        ]);

        Product::create([
            'product_code'  => 'MEK-000002',
            'name'          => 'Cıvata M5',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 100,
        ]);

        $nextCode = $this->service->generate($cat);
        $this->assertEquals('MEK-000003', $nextCode);
    }

    public function test_code_generator_includes_soft_deleted_products_to_prevent_duplicate_key_errors(): void
    {
        $cat = Category::create(['name' => 'Kırtasiye', 'code' => 'KRT']);

        $product1 = Product::create([
            'product_code'  => 'KRT-000001',
            'name'          => 'Defter',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $product2 = Product::create([
            'product_code'  => 'KRT-000002',
            'name'          => 'Kalem',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        // Soft delete the latest product (KRT-000002)
        $product2->delete();

        // Generating next code should return KRT-000003 (NOT KRT-000002 which would crash on UNIQUE key)
        $nextCode = $this->service->generate($cat);
        $this->assertEquals('KRT-000003', $nextCode);
    }
}
