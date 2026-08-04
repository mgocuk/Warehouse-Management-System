<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $manager;
    protected User $viewer;
    protected User $inactiveUser;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole   = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Depo Sorumlusu']);
        $viewerRole  = Role::create(['name' => 'Görüntüleme']);

        $this->admin   = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);
        $this->manager = User::factory()->create(['role_id' => $managerRole->id, 'is_active' => true]);
        $this->viewer  = User::factory()->create(['role_id' => $viewerRole->id, 'is_active' => true]);

        $this->inactiveUser = User::factory()->create([
            'role_id'   => $adminRole->id,
            'is_active' => false, // Inactive account
        ]);
    }

    public function test_admin_has_access_to_user_management_and_audit_logs(): void
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get('/audit-logs');
        $response->assertStatus(200);
    }

    public function test_manager_is_forbidden_from_user_management_and_audit_logs(): void
    {
        $response = $this->actingAs($this->manager)->get('/users');
        $response->assertStatus(403);

        $response = $this->actingAs($this->manager)->get('/audit-logs');
        $response->assertStatus(403);
    }

    public function test_viewer_is_forbidden_from_user_management_and_audit_logs(): void
    {
        $response = $this->actingAs($this->viewer)->get('/users');
        $response->assertStatus(403);

        $response = $this->actingAs($this->viewer)->get('/audit-logs');
        $response->assertStatus(403);
    }

    public function test_viewer_is_forbidden_from_product_creation(): void
    {
        $cat = Category::create(['name' => 'Genel', 'code' => 'GNL']);

        $response = $this->actingAs($this->viewer)->post('/products', [
            'name'          => 'Yetkisiz Ürün',
            'category_id'   => $cat->id,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);

        $response->assertStatus(403);
    }

    public function test_viewer_is_forbidden_from_stock_in_operation(): void
    {
        $response = $this->actingAs($this->viewer)->post('/stocks/in', [
            'product_id'   => 1,
            'warehouse_id' => 1,
            'shelf_id'     => 1,
            'quantity'     => 50,
        ]);

        $response->assertStatus(403);
    }

    public function test_viewer_is_forbidden_from_category_creation(): void
    {
        $response = $this->actingAs($this->viewer)->post('/categories', [
            'name' => 'Yetkisiz Kategori',
            'code' => 'YET',
        ]);

        $response->assertStatus(403);
    }

    public function test_viewer_is_forbidden_from_warehouse_creation(): void
    {
        $response = $this->actingAs($this->viewer)->post('/warehouses', [
            'name' => 'Yetkisiz Depo',
        ]);

        $response->assertStatus(403);
    }

    public function test_inactive_user_is_logged_out_and_redirected_to_login(): void
    {
        $response = $this->actingAs($this->inactiveUser)->get('/dashboard');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_unauthenticated_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }
}
