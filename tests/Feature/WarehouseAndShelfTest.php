<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WarehouseAndShelfTest extends TestCase
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

    public function test_warehouse_index_page_is_accessible(): void
    {
        Warehouse::create(['name' => 'Merkez Depo', 'location' => 'Bina A']);

        $response = $this->actingAs($this->viewer)->get('/warehouses');
        $response->assertStatus(200);
        $response->assertSee('Merkez Depo');
    }

    public function test_manager_can_create_warehouse(): void
    {
        $response = $this->actingAs($this->manager)->post('/warehouses', [
            'name'        => 'Yeni Malzeme Deposu',
            'location'    => 'Bina C - Zemin Kat',
            'description' => 'Ağır sanayi parçaları',
        ]);

        $response->assertRedirect('/warehouses');
        $this->assertDatabaseHas('warehouses', [
            'name'     => 'Yeni Malzeme Deposu',
            'location' => 'Bina C - Zemin Kat',
        ]);
    }

    public function test_warehouse_show_page_displays_shelves_and_inventory(): void
    {
        $wh = Warehouse::create(['name' => 'Detay Deposu', 'location' => 'Bina B']);
        $shelf = Shelf::create([
            'warehouse_id' => $wh->id,
            'name'         => 'R-100',
            'code'         => 'R100',
        ]);

        $response = $this->actingAs($this->viewer)->get("/warehouses/{$wh->id}");
        $response->assertStatus(200);
        $response->assertSee('Detay Deposu');
        $response->assertSee('R-100');
    }

    public function test_warehouse_show_page_supports_sorting_headers(): void
    {
        $wh = Warehouse::create(['name' => 'Sıralama Deposu']);
        $cat = Category::create(['name' => 'Genel', 'code' => 'GNL']);
        $shelf = Shelf::create(['warehouse_id' => $wh->id, 'name' => 'R1']);

        $p1 = Product::create(['product_code' => 'GNL-000001', 'name' => 'Alpha', 'category_id' => $cat->id, 'unit' => 'Adet', 'minimum_stock' => 5]);
        $p2 = Product::create(['product_code' => 'GNL-000002', 'name' => 'Zeta', 'category_id' => $cat->id, 'unit' => 'Adet', 'minimum_stock' => 5]);

        Stock::create(['product_id' => $p1->id, 'warehouse_id' => $wh->id, 'shelf_id' => $shelf->id, 'quantity' => 10]);
        Stock::create(['product_id' => $p2->id, 'warehouse_id' => $wh->id, 'shelf_id' => $shelf->id, 'quantity' => 20]);

        $response = $this->actingAs($this->viewer)->get("/warehouses/{$wh->id}?sort=name&direction=desc");
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Zeta', 'Alpha']);
    }

    public function test_warehouse_deletion_fails_if_warehouse_contains_stock_quantity(): void
    {
        $wh = Warehouse::create(['name' => 'Dolu Depo']);
        $cat = Category::create(['name' => 'Genel', 'code' => 'GNL']);
        $shelf = Shelf::create(['warehouse_id' => $wh->id, 'name' => 'R1']);
        $product = Product::create(['product_code' => 'GNL-000001', 'name' => 'Ürün', 'category_id' => $cat->id, 'unit' => 'Adet', 'minimum_stock' => 5]);

        Stock::create([
            'product_id'   => $product->id,
            'warehouse_id' => $wh->id,
            'shelf_id'     => $shelf->id,
            'quantity'     => 15,
        ]);

        $response = $this->actingAs($this->manager)->delete("/warehouses/{$wh->id}");
        $response->assertRedirect('/warehouses');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('warehouses', ['id' => $wh->id]);
    }

    public function test_manager_can_create_shelf_in_warehouse(): void
    {
        $wh = Warehouse::create(['name' => 'Raf Deposu']);

        $response = $this->actingAs($this->manager)->post('/shelves', [
            'warehouse_id' => $wh->id,
            'name'         => 'Sensör Raflığı',
            'code'         => 'S1',
            'floor'        => '2. Kat',
            'row'          => '3. Sıra',
            'description'  => 'Hassas sensörler',
        ]);

        $response->assertRedirect('/shelves');
        $this->assertDatabaseHas('shelves', [
            'warehouse_id' => $wh->id,
            'name'         => 'Sensör Raflığı',
            'code'         => 'S1',
            'floor'        => '2. Kat',
        ]);
    }

    public function test_manager_can_delete_empty_shelf(): void
    {
        $wh = Warehouse::create(['name' => 'Raf Deposu']);
        $shelf = Shelf::create(['warehouse_id' => $wh->id, 'name' => 'Silinecek Raf']);

        $response = $this->actingAs($this->manager)->delete("/shelves/{$shelf->id}");
        $response->assertRedirect('/shelves');

        $this->assertDatabaseMissing('shelves', ['id' => $shelf->id]);
    }
}
