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

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    protected User $manager;
    protected User $viewer;
    protected Category $category;
    protected Warehouse $warehouse1;
    protected Warehouse $warehouse2;
    protected Shelf $shelf1;
    protected Shelf $shelf2;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $managerRole = Role::create(['name' => 'Depo Sorumlusu']);
        $viewerRole  = Role::create(['name' => 'Görüntüleme']);

        $this->manager = User::factory()->create(['role_id' => $managerRole->id]);
        $this->viewer  = User::factory()->create(['role_id' => $viewerRole->id]);

        $this->category = Category::create([
            'name' => 'Donanım',
            'code' => 'DON',
        ]);

        $this->warehouse1 = Warehouse::create(['name' => 'Ana Depo', 'location' => 'Bina A']);
        $this->warehouse2 = Warehouse::create(['name' => 'Yedek Depo', 'location' => 'Bina B']);

        $this->shelf1 = Shelf::create([
            'warehouse_id' => $this->warehouse1->id,
            'name'         => 'A-Raf-1',
            'code'         => 'AR1',
        ]);

        $this->shelf2 = Shelf::create([
            'warehouse_id' => $this->warehouse2->id,
            'name'         => 'B-Raf-1',
            'code'         => 'BR1',
        ]);

        $this->product = Product::create([
            'product_code'  => 'DON-000001',
            'name'          => 'Raspberry Pi Pico',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);
    }

    public function test_stock_in_increases_quantity_and_creates_movement_log(): void
    {
        $response = $this->actingAs($this->manager)->post('/stocks/in', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 100,
            'reason'       => 'Satın Alma Stok Girişi',
        ]);

        $response->assertRedirect('/stocks');

        $this->assertDatabaseHas('stocks', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 100,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id'    => $this->product->id,
            'warehouse_id'  => $this->warehouse1->id,
            'shelf_id'      => $this->shelf1->id,
            'user_id'       => $this->manager->id,
            'type'          => 'in',
            'old_quantity'  => 0,
            'new_quantity'  => 100,
            'change_amount' => 100,
            'reason'        => 'Satın Alma Stok Girişi',
        ]);
    }

    public function test_multiple_stock_ins_accumulate_quantity_correctly(): void
    {
        $this->actingAs($this->manager)->post('/stocks/in', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 30,
        ]);

        $this->actingAs($this->manager)->post('/stocks/in', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 20,
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $this->product->id,
            'shelf_id'   => $this->shelf1->id,
            'quantity'   => 50,
        ]);
    }

    public function test_stock_in_fails_if_shelf_does_not_belong_to_selected_warehouse(): void
    {
        // shelf2 belongs to warehouse2, not warehouse1
        $response = $this->actingAs($this->manager)->post('/stocks/in', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf2->id,
            'quantity'     => 10,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_stock_out_decreases_quantity_and_creates_negative_change_movement(): void
    {
        Stock::create([
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 50,
        ]);

        $response = $this->actingAs($this->manager)->post('/stocks/out', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 15,
            'reason'       => 'Atölye Kullanımı Çıkışı',
        ]);

        $response->assertRedirect('/stocks');

        $this->assertDatabaseHas('stocks', [
            'product_id' => $this->product->id,
            'shelf_id'   => $this->shelf1->id,
            'quantity'   => 35,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id'    => $this->product->id,
            'type'          => 'out',
            'old_quantity'  => 50,
            'new_quantity'  => 35,
            'change_amount' => -15,
            'reason'        => 'Atölye Kullanımı Çıkışı',
        ]);
    }

    public function test_stock_out_fails_when_quantity_exceeds_available_stock(): void
    {
        Stock::create([
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 10,
        ]);

        // Trying to remove 50 when only 10 is available
        $response = $this->actingAs($this->manager)->post('/stocks/out', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 50,
            'reason'       => 'Aşırı Çıkış İsteği',
        ]);

        $this->assertDatabaseHas('stocks', [
            'product_id' => $this->product->id,
            'shelf_id'   => $this->shelf1->id,
            'quantity'   => 10,
        ]);
    }

    public function test_stock_out_requires_reason(): void
    {
        Stock::create([
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 20,
        ]);

        $response = $this->actingAs($this->manager)->post('/stocks/out', [
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 5,
            'reason'       => '', // Empty reason
        ]);

        $response->assertSessionHasErrors(['reason']);
    }

    public function test_stock_transfer_creates_dual_movement_records(): void
    {
        Stock::create([
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 80,
        ]);

        $response = $this->actingAs($this->manager)->post('/stocks/transfer', [
            'product_id'        => $this->product->id,
            'from_warehouse_id' => $this->warehouse1->id,
            'from_shelf_id'     => $this->shelf1->id,
            'to_warehouse_id'   => $this->warehouse2->id,
            'to_shelf_id'       => $this->shelf2->id,
            'quantity'          => 25,
            'reason'            => 'Depolar Arası Düzenleme',
        ]);

        $response->assertRedirect('/stocks');

        // Source shelf deducted
        $this->assertDatabaseHas('stocks', [
            'shelf_id' => $this->shelf1->id,
            'quantity' => 55,
        ]);

        // Destination shelf credited
        $this->assertDatabaseHas('stocks', [
            'shelf_id' => $this->shelf2->id,
            'quantity' => 25,
        ]);

        // Dual audit log check
        $this->assertDatabaseHas('stock_movements', [
            'shelf_id'      => $this->shelf1->id,
            'type'          => 'transfer',
            'change_amount' => -25,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'shelf_id'      => $this->shelf2->id,
            'type'          => 'transfer',
            'change_amount' => 25,
        ]);
    }

    public function test_stock_transfer_fails_when_same_source_and_destination_shelf_selected(): void
    {
        Stock::create([
            'product_id'   => $this->product->id,
            'warehouse_id' => $this->warehouse1->id,
            'shelf_id'     => $this->shelf1->id,
            'quantity'     => 50,
        ]);

        $response = $this->actingAs($this->manager)->post('/stocks/transfer', [
            'product_id'        => $this->product->id,
            'from_warehouse_id' => $this->warehouse1->id,
            'from_shelf_id'     => $this->shelf1->id,
            'to_warehouse_id'   => $this->warehouse1->id,
            'to_shelf_id'       => $this->shelf1->id, // Same shelf
            'quantity'          => 10,
        ]);

        $response->assertSessionHas('error');
    }

    public function test_stock_movements_page_lists_all_historical_records(): void
    {
        StockMovement::create([
            'product_id'    => $this->product->id,
            'warehouse_id'  => $this->warehouse1->id,
            'shelf_id'      => $this->shelf1->id,
            'user_id'       => $this->manager->id,
            'type'          => 'in',
            'old_quantity'  => 0,
            'new_quantity'  => 100,
            'change_amount' => 100,
            'reason'        => 'Test Kaydı',
        ]);

        $response = $this->actingAs($this->viewer)->get('/stocks/movements');
        $response->assertStatus(200);
        $response->assertSee('Test Kaydı');
        $response->assertSee('Raspberry Pi Pico');
    }

    public function test_stock_movements_filter_by_type(): void
    {
        StockMovement::create([
            'product_id'    => $this->product->id,
            'warehouse_id'  => $this->warehouse1->id,
            'shelf_id'      => $this->shelf1->id,
            'user_id'       => $this->manager->id,
            'type'          => 'in',
            'old_quantity'  => 0,
            'new_quantity'  => 100,
            'change_amount' => 100,
            'reason'        => 'Özel Giriş',
        ]);

        StockMovement::create([
            'product_id'    => $this->product->id,
            'warehouse_id'  => $this->warehouse1->id,
            'shelf_id'      => $this->shelf1->id,
            'user_id'       => $this->manager->id,
            'type'          => 'out',
            'old_quantity'  => 100,
            'new_quantity'  => 80,
            'change_amount' => -20,
            'reason'        => 'Özel Çıkış',
        ]);

        $response = $this->actingAs($this->viewer)->get('/stocks/movements?type=in');
        $response->assertStatus(200);
        $response->assertSee('Özel Giriş');
        $response->assertDontSee('Özel Çıkış');
    }
}
