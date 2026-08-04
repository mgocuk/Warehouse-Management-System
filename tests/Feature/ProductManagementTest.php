<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCodeHistory;
use App\Models\Role;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $viewer;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $managerRole = Role::create(['name' => 'Depo Sorumlusu']);
        $viewerRole  = Role::create(['name' => 'Görüntüleme']);

        $this->manager = User::factory()->create(['role_id' => $managerRole->id]);
        $this->viewer  = User::factory()->create(['role_id' => $viewerRole->id]);

        $this->category = Category::create([
            'name' => 'Elektronik Bileşenler',
            'code' => 'ELK',
            'description' => 'Elektronik malzeme grubu',
        ]);
    }

    public function test_product_list_is_accessible_by_authenticated_users(): void
    {
        $response = $this->actingAs($this->viewer)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Ürün Kataloğu');
    }

    public function test_manager_can_access_create_product_page(): void
    {
        $response = $this->actingAs($this->manager)->get('/products/create');
        $response->assertStatus(200);
        $response->assertSee('Yeni Ürün Ekle');
    }

    public function test_product_creation_auto_generates_code_with_category_prefix(): void
    {
        $response = $this->actingAs($this->manager)->post('/products', [
            'name'          => 'STM32F407G Microcontroller',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 15,
            'description'   => '32-bit ARM Cortex-M4 MCU',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name'         => 'STM32F407G Microcontroller',
            'product_code' => 'ELK-000001',
            'unit'         => 'Adet',
            'minimum_stock'=> 15,
        ]);
    }

    public function test_product_creation_validation_requires_name_and_category(): void
    {
        $response = $this->actingAs($this->manager)->post('/products', [
            'name'          => '',
            'category_id'   => '',
            'unit'          => '',
            'minimum_stock' => -5,
        ]);

        $response->assertSessionHasErrors(['name', 'category_id', 'unit', 'minimum_stock']);
    }

    public function test_product_creation_fails_with_invalid_category_id(): void
    {
        $response = $this->actingAs($this->manager)->post('/products', [
            'name'          => 'Test Ürünü',
            'category_id'   => 99999,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);

        $response->assertSessionHasErrors(['category_id']);
    }

    public function test_manager_can_update_product_information(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Eski Ürün Adı',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->manager)->put("/products/{$product->id}", [
            'product_code'  => 'ELK-000001',
            'name'          => 'Yeni Ürün Adı',
            'category_id'   => $this->category->id,
            'unit'          => 'Paket',
            'minimum_stock' => 20,
            'description'   => 'Güncellendi',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id'            => $product->id,
            'name'          => 'Yeni Ürün Adı',
            'unit'          => 'Paket',
            'minimum_stock' => 20,
        ]);
    }

    public function test_product_code_change_records_history_log_with_changed_by(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Direnç 10k',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 100,
        ]);

        $response = $this->actingAs($this->manager)->put("/products/{$product->id}", [
            'product_code'       => 'ELK-000099',
            'name'               => 'Direnç 10k',
            'category_id'        => $this->category->id,
            'unit'               => 'Adet',
            'minimum_stock'      => 100,
            'code_change_reason' => 'Kategori Kodlama Revizyonu',
        ]);

        $response->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'id'           => $product->id,
            'product_code' => 'ELK-000099',
        ]);

        $this->assertDatabaseHas('product_code_histories', [
            'product_id' => $product->id,
            'old_code'   => 'ELK-000001',
            'new_code'   => 'ELK-000099',
            'changed_by' => $this->manager->id,
            'reason'     => 'Kategori Kodlama Revizyonu',
        ]);
    }

    public function test_duplicate_product_code_validation_fails_on_update(): void
    {
        $p1 = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Ürün 1',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $p2 = Product::create([
            'product_code'  => 'ELK-000002',
            'name'          => 'Ürün 2',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        // Attempting to change p2's code to p1's code should fail
        $response = $this->actingAs($this->manager)->put("/products/{$p2->id}", [
            'product_code'  => 'ELK-000001',
            'name'          => 'Ürün 2',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $response->assertSessionHasErrors(['product_code']);
    }

    public function test_product_soft_delete_moves_product_to_trash(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Silinecek Ürün',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->manager)->delete("/products/{$product->id}");
        $response->assertRedirect('/products');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_trashed_products_can_be_viewed_in_trash_page(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Çöpteki Ürün',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);
        $product->delete();

        $response = $this->actingAs($this->manager)->get('/products/trash');
        $response->assertStatus(200);
        $response->assertSee('Çöpteki Ürün');
    }

    public function test_manager_can_restore_soft_deleted_product(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Kurtarılacak Ürün',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);
        $product->delete();

        $response = $this->actingAs($this->manager)->post("/products/{$product->id}/restore");
        $response->assertRedirect('/products/trash');

        $this->assertDatabaseHas('products', [
            'id'         => $product->id,
            'deleted_at' => null,
        ]);
    }

    public function test_manager_can_force_delete_product_safely(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Kalıcı Silinecek Ürün',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);
        $product->delete();

        $response = $this->actingAs($this->manager)->delete("/products/{$product->id}/force-delete");
        $response->assertRedirect('/products/trash');

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_empty_trash_removes_all_soft_deleted_products(): void
    {
        $p1 = Product::create([
            'product_code' => 'ELK-000001',
            'name'         => 'Çöp 1',
            'category_id'  => $this->category->id,
            'unit'         => 'Adet',
            'minimum_stock'=> 5,
        ]);
        $p2 = Product::create([
            'product_code' => 'ELK-000002',
            'name'         => 'Çöp 2',
            'category_id'  => $this->category->id,
            'unit'         => 'Adet',
            'minimum_stock'=> 5,
        ]);
        $p1->delete();
        $p2->delete();

        $response = $this->actingAs($this->manager)->delete('/products/empty-trash');
        $response->assertRedirect('/products/trash');

        $this->assertDatabaseMissing('products', ['id' => $p1->id]);
        $this->assertDatabaseMissing('products', ['id' => $p2->id]);
    }

    public function test_product_search_filters_results_by_name_or_code(): void
    {
        Product::create([
            'product_code'  => 'ELK-000100',
            'name'          => 'Özel Kondansatör',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);
        Product::create([
            'product_code'  => 'ELK-000200',
            'name'          => 'Genel Transistör',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->viewer)->get('/products?search=Kondansatör');
        $response->assertStatus(200);
        $response->assertSee('Özel Kondansatör');
        $response->assertDontSee('Genel Transistör');
    }

    public function test_product_filter_by_category(): void
    {
        $cat2 = Category::create(['name' => 'Mekanik', 'code' => 'MEK']);

        Product::create([
            'product_code'  => 'ELK-000100',
            'name'          => 'Elektronik Kart',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);
        Product::create([
            'product_code'  => 'MEK-000100',
            'name'          => 'Cıvata M6',
            'category_id'   => $cat2->id,
            'unit'          => 'Adet',
            'minimum_stock' => 100,
        ]);

        $response = $this->actingAs($this->viewer)->get("/products?category_id={$cat2->id}");
        $response->assertStatus(200);
        $response->assertSee('Cıvata M6');
        $response->assertDontSee('Elektronik Kart');
    }

    public function test_product_show_page_displays_details_and_histories(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000100',
            'name'          => 'Detay Ürünü',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
            'description'   => 'Açıklama Detayı',
        ]);

        $response = $this->actingAs($this->viewer)->get("/products/{$product->id}");
        $response->assertStatus(200);
        $response->assertSee('Detay Ürünü');
        $response->assertSee('Açıklama Detayı');
    }
}
