<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Shelf;
use App\Models\User;
use App\Models\Warehouse;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $managerRole = Role::create(['name' => 'Depo Sorumlusu']);
        $viewerRole  = Role::create(['name' => 'Görüntüleme']);

        $this->manager = User::factory()->create(['role_id' => $managerRole->id]);
        $this->viewer  = User::factory()->create(['role_id' => $viewerRole->id]);
    }

    public function test_category_list_is_accessible_by_authenticated_users(): void
    {
        Category::create(['name' => 'Kimyasallar', 'code' => 'KIM']);

        $response = $this->actingAs($this->viewer)->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Kimyasallar');
        $response->assertSee('KIM');
    }

    public function test_manager_can_create_category_and_code_is_uppercase(): void
    {
        $response = $this->actingAs($this->manager)->post('/categories', [
            'name'        => 'Robotik Parçaları',
            'code'        => 'rob',
            'description' => 'Motorlar ve sensörler',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Robotik Parçaları',
            'code' => 'ROB', // Converted to uppercase
        ]);
    }

    public function test_category_code_must_be_unique(): void
    {
        Category::create(['name' => 'Mekanik', 'code' => 'MEK']);

        $response = $this->actingAs($this->manager)->post('/categories', [
            'name' => 'Yeni Mekanik',
            'code' => 'MEK',
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_manager_can_update_category(): void
    {
        $cat = Category::create(['name' => 'Eski Ad', 'code' => 'ESK']);

        $response = $this->actingAs($this->manager)->put("/categories/{$cat->id}", [
            'name' => 'Yeni Ad',
            'code' => 'YEN',
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'id'   => $cat->id,
            'name' => 'Yeni Ad',
            'code' => 'YEN',
        ]);
    }

    public function test_category_cannot_be_deleted_if_it_contains_active_products(): void
    {
        $cat = Category::create(['name' => 'Plastik', 'code' => 'PLS']);
        Product::create([
            'product_code'  => 'PLS-000001',
            'name'          => 'Plastik Kutu',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $response = $this->actingAs($this->manager)->delete("/categories/{$cat->id}");
        $response->assertRedirect('/categories');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }

    public function test_category_cannot_be_deleted_if_it_contains_soft_deleted_products(): void
    {
        $cat = Category::create(['name' => 'Kırtasiye', 'code' => 'KRT']);
        $product = Product::create([
            'product_code'  => 'KRT-000001',
            'name'          => 'Kalem',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);
        $product->delete(); // Soft delete product

        $response = $this->actingAs($this->manager)->delete("/categories/{$cat->id}");
        $response->assertRedirect('/categories');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $cat->id]);
    }

    public function test_category_can_be_deleted_if_it_has_no_products(): void
    {
        $cat = Category::create(['name' => 'Boş Kategori', 'code' => 'BOS']);

        $response = $this->actingAs($this->manager)->delete("/categories/{$cat->id}");
        $response->assertRedirect('/categories');

        $this->assertDatabaseMissing('categories', ['id' => $cat->id]);
    }

    public function test_shelf_api_endpoint_returns_shelves_by_warehouse_id(): void
    {
        $warehouse = Warehouse::create(['name' => 'API Depo']);
        $shelf = Shelf::create([
            'warehouse_id' => $warehouse->id,
            'name'         => 'Test Rafı',
            'code'         => 'TR1',
        ]);

        $response = $this->actingAs($this->viewer)->getJson("/shelves/by-warehouse/{$warehouse->id}");
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id'   => $shelf->id,
            'name' => 'Test Rafı',
            'code' => 'TR1',
        ]);
    }
}
