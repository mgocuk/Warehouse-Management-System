<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCodeHistory;
use App\Models\Role;
use App\Models\Shelf;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;
    protected Role $managerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole   = Role::create(['name' => 'Admin']);
        $this->managerRole = Role::create(['name' => 'Depo Sorumlusu']);

        $this->admin = User::factory()->create(['role_id' => $this->adminRole->id, 'is_active' => true]);
    }

    public function test_admin_can_view_users_list(): void
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('Sistem Kullanıcıları');
    }

    public function test_admin_can_create_new_user(): void
    {
        $response = $this->actingAs($this->admin)->post('/users', [
            'name'     => 'Mehmet Demir',
            'email'    => 'mehmet@bilimmerkezi.org',
            'password' => '12345678',
            'role_id'  => $this->managerRole->id,
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'name'      => 'Mehmet Demir',
            'email'     => 'mehmet@bilimmerkezi.org',
            'role_id'   => $this->managerRole->id,
            'is_active' => true,
        ]);
    }

    public function test_user_creation_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'mevcut@bilimmerkezi.org']);

        $response = $this->actingAs($this->admin)->post('/users', [
            'name'     => 'Aynı Eposta',
            'email'    => 'mevcut@bilimmerkezi.org',
            'password' => '12345678',
            'role_id'  => $this->managerRole->id,
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_update_user_role_and_active_status(): void
    {
        $user = User::factory()->create(['role_id' => $this->managerRole->id, 'is_active' => true]);

        $response = $this->actingAs($this->admin)->put("/users/{$user->id}", [
            'name'      => 'Mehmet Demir Güncel',
            'email'     => $user->email,
            'role_id'   => $this->adminRole->id,
            'is_active' => '0', // Deactivate
        ]);

        $response->assertRedirect('/users');
        $this->assertDatabaseHas('users', [
            'id'        => $user->id,
            'name'      => 'Mehmet Demir Güncel',
            'role_id'   => $this->adminRole->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_cannot_deactivate_or_demote_their_own_account(): void
    {
        $response = $this->actingAs($this->admin)->put("/users/{$this->admin->id}", [
            'name'      => $this->admin->name,
            'email'     => $this->admin->email,
            'role_id'   => $this->managerRole->id, // Demote self
            'is_active' => '0',                     // Deactivate self
        ]);

        $response->assertRedirect('/users');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', [
            'id'        => $this->admin->id,
            'role_id'   => $this->adminRole->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $response = $this->actingAs($this->admin)->delete("/users/{$this->admin->id}");
        $response->assertRedirect('/users');
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    public function test_admin_can_hard_delete_other_user_and_unlink_foreign_keys(): void
    {
        $user = User::factory()->create(['role_id' => $this->managerRole->id]);

        $category  = Category::create(['name' => 'Test', 'code' => 'TST']);
        $warehouse = Warehouse::create(['name' => 'Test Depo']);
        $shelf     = Shelf::create(['warehouse_id' => $warehouse->id, 'name' => 'S1']);
        $product   = Product::create(['product_code' => 'TST-000001', 'name' => 'Ürün', 'category_id' => $category->id, 'unit' => 'Adet', 'minimum_stock' => 5]);

        // Attach related records to this user
        $log = AuditLog::create([
            'user_id'    => $user->id,
            'action'     => 'CREATE',
            'table_name' => 'products',
            'record_id'  => $product->id,
        ]);

        $movement = StockMovement::create([
            'product_id'    => $product->id,
            'warehouse_id'  => $warehouse->id,
            'shelf_id'      => $shelf->id,
            'user_id'       => $user->id,
            'type'          => 'in',
            'old_quantity'  => 0,
            'new_quantity'  => 10,
            'change_amount' => 10,
        ]);

        $history = ProductCodeHistory::create([
            'product_id' => $product->id,
            'old_code'   => 'TST-000000',
            'new_code'   => 'TST-000001',
            'reason'     => 'Test',
            'changed_by' => $user->id,
        ]);

        // Delete user
        $response = $this->actingAs($this->admin)->delete("/users/{$user->id}");
        $response->assertRedirect('/users');

        // User is permanently removed from users table
        $this->assertDatabaseMissing('users', ['id' => $user->id]);

        // Related records had foreign keys set to NULL cleanly without SQL error
        $this->assertDatabaseHas('audit_logs', ['id' => $log->id, 'user_id' => null]);
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id, 'user_id' => null]);
        $this->assertDatabaseHas('product_code_histories', ['id' => $history->id, 'changed_by' => null]);
    }
}
