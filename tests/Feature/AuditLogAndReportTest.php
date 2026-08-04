<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\Warehouse;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogAndReportTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'Admin']);
        $this->admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);

        $this->category = Category::create(['name' => 'Test Kat', 'code' => 'TST']);
    }

    public function test_creating_product_triggers_create_audit_log(): void
    {
        $this->actingAs($this->admin)->post('/products', [
            'name'          => 'Audit Test Ürünü',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'     => 'CREATE',
            'table_name' => 'products',
            'user_id'    => $this->admin->id,
        ]);
    }

    public function test_updating_product_triggers_update_audit_log(): void
    {
        $product = Product::create([
            'product_code'  => 'TST-000001',
            'name'          => 'Eski Ad',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);

        $this->actingAs($this->admin)->put("/products/{$product->id}", [
            'product_code'  => 'TST-000001',
            'name'          => 'Yeni Ad',
            'category_id'   => $this->category->id,
            'unit'          => 'Adet',
            'minimum_stock' => 10,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'action'     => 'UPDATE',
            'table_name' => 'products',
            'record_id'  => $product->id,
        ]);
    }

    public function test_audit_log_format_data_translates_keys_to_turkish(): void
    {
        $log = AuditLog::create([
            'user_id'    => $this->admin->id,
            'action'     => 'UPDATE',
            'table_name' => 'users',
            'record_id'  => $this->admin->id,
            'old_data'   => ['is_active' => true],
            'new_data'   => ['is_active' => false],
        ]);

        $formattedOld = $log->formatData($log->old_data);
        $formattedNew = $log->formatData($log->new_data);

        $this->assertStringContainsString('Durum:', $formattedOld);
        $this->assertStringContainsString('AKTİF', $formattedOld);
        $this->assertStringContainsString('PASİF', $formattedNew);
    }

    public function test_audit_logs_can_be_filtered_by_action_and_table_name(): void
    {
        AuditLog::create(['action' => 'CREATE', 'table_name' => 'products', 'record_id' => 1]);
        AuditLog::create(['action' => 'UPDATE', 'table_name' => 'users', 'record_id' => 2]);

        $response = $this->actingAs($this->admin)->get('/audit-logs?action=CREATE&table_name=products');
        $response->assertStatus(200);
        $response->assertSee('Ürünler Kataloğu');
        $response->assertDontSee('Kullanıcı Hesapları');
    }

    public function test_report_index_page_is_accessible(): void
    {
        $response = $this->actingAs($this->admin)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Raporlar ve Dışa Aktarma');
    }

    public function test_csv_export_returns_streamed_csv_response(): void
    {
        $response = $this->actingAs($this->admin)->get('/reports/export-csv?format=csv');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=windows-1254');
    }
}
