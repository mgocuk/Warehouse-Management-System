<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $viewer;
    protected Category $category;
    protected Warehouse $warehouse1;
    protected Warehouse $warehouse2;
    protected Shelf $shelf1;
    protected Shelf $shelf2;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole   = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Depo Sorumlusu']);
        $viewerRole  = Role::create(['name' => 'Görüntüleme']);

        $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
        $this->manager = User::factory()->create(['role_id' => $managerRole->id]);
        $this->viewer = User::factory()->create(['role_id' => $viewerRole->id]);

        $this->category = Category::create([
            'name' => 'Elektronik',
            'code' => 'ELK',
            'description' => 'Elektronik parçalar',
        ]);

        $this->warehouse1 = Warehouse::create(['name' => 'Ana Depo', 'location' => 'Bina A']);
        $this->warehouse2 = Warehouse::create(['name' => 'Yedek Depo', 'location' => 'Bina B']);

        $this->shelf1 = Shelf::create([
            'warehouse_id' => $this->warehouse1->id,
            'name' => 'A-01',
            'code' => 'A1',
        ]);

        $this->shelf2 = Shelf::create([
            'warehouse_id' => $this->warehouse2->id,
            'name' => 'B-01',
            'code' => 'B1',
        ]);
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_manager_can_create_product_with_auto_generated_code(): void
    {
        $response = $this->actingAs($this->manager)->post('/products', [
            'name'          => 'Raspberry Pi 4',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
            'description'   => 'Mini Bilgisayar',
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'name'         => 'Raspberry Pi 4',
            'product_code' => 'ELK-000001',
        ]);
    }

    public function test_stock_in_increases_inventory(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000001',
            'name'          => 'Sensör Kartı',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);

        $response = $this->actingAs($this->manager)->post('/stocks/in', [
            'product_id'   => $product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 50,
            'reason'       => 'İlk Stok Girişi',
        ]);

        $response->assertRedirect('/stocks');

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'shelf_id'   => $this->shelf1->id,
            'quantity'   => 50,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id'    => $product->id,
            'type'          => 'in',
            'change_amount' => 50,
        ]);
    }

    public function test_stock_transfer_between_shelves_logs_both_movements(): void
    {
        $product = Product::create([
            'product_code'  => 'ELK-000002',
            'name'          => 'Motor Sürücü',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        // Create initial stock in shelf 1
        Stock::create([
            'product_id'   => $product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 100,
        ]);

        $response = $this->actingAs($this->manager)->post('/stocks/transfer', [
            'product_id'        => $product->id,
            'from_warehouse_id' => $this->warehouse1->id,
            'from_shelf_id'     => $this->shelf1->id,
            'to_warehouse_id'   => $this->warehouse2->id,
            'to_shelf_id'       => $this->shelf2->id,
            'quantity'          => 30,
            'reason'            => 'Proje İhtiyacı Transferi',
        ]);

        $response->assertRedirect('/stocks');

        // Check stock quantities
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'shelf_id'   => $this->shelf1->id,
            'quantity'   => 70,
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'shelf_id'   => $this->shelf2->id,
            'quantity'   => 30,
        ]);

        // Verify TWO movement logs (source -30 and destination +30)
        $this->assertDatabaseHas('stock_movements', [
            'product_id'    => $product->id,
            'shelf_id'      => $this->shelf1->id,
            'type'          => 'transfer',
            'change_amount' => -30,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id'    => $product->id,
            'shelf_id'      => $this->shelf2->id,
            'type'          => 'transfer',
            'change_amount' => 30,
        ]);
    }

    public function test_viewer_role_cannot_create_or_delete_product(): void
    {
        $response = $this->actingAs($this->viewer)->post('/products', [
            'name'          => 'Yetkisiz Ürün',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 5,
        ]);

        $response->assertStatus(403);
    }
}
