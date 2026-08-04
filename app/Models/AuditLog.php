<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_data',
        'new_data',
        'reason',
        'ip_address',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'Misafir / Sistem']);
    }

    /**
     * Get human-readable Turkish table name
     */
    public function getTableLabelAttribute(): string
    {
        $tables = [
            'products'               => 'Ürünler Kataloğu',
            'stocks'                 => 'Stok Durumu',
            'stock_movements'        => 'Stok Hareketleri',
            'warehouses'             => 'Depo Alanları',
            'shelves'                => 'Raf Tanımları',
            'categories'             => 'Kategoriler',
            'users'                  => 'Kullanıcı Hesapları',
            'product_code_histories' => 'Ürün Kod Geçmişi',
        ];

        return $tables[$this->table_name] ?? ucfirst($this->table_name);
    }

    /**
     * Get human-readable Turkish action label
     */
    public function getActionLabelAttribute(): string
    {
        $actions = [
            'CREATE'       => 'YENİ KAYIT (+)',
            'UPDATE'       => 'GÜNCELLEME (✎)',
            'DELETE'       => 'SİLİNDİ (✕)',
            'SOFT_DELETE'  => 'ÇÖPE TAŞINDI (🗑)',
            'RESTORE'      => 'GERİ YÜKLENDİ (↺)',
            'FORCE_DELETE' => 'KALICI SİLİNDİ (✕)',
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Format raw JSON data array into readable Turkish string
     */
    public function formatData(?array $data): string
    {
        if (empty($data)) {
            return '-';
        }

        $fieldLabels = [
            'name'           => 'Ad/İsim',
            'email'          => 'E-Posta',
            'is_active'      => 'Durum',
            'role_id'        => 'Rol',
            'quantity'       => 'Miktar',
            'product_code'   => 'Ürün Kodu',
            'minimum_stock'  => 'Min. Stok',
            'unit'           => 'Birim',
            'description'    => 'Açıklama',
            'location'       => 'Lokasyon',
            'password'       => 'Şifre',
            'remember_token' => 'Oturum Jetonu',
            'category_id'    => 'Kategori ID',
            'warehouse_id'   => 'Depo ID',
            'shelf_id'       => 'Raf ID',
            'code'           => 'Kategori Kodu',
            'old_code'       => 'Eski Kod',
            'new_code'       => 'Yeni Kod',
            'reason'         => 'Açıklama/Neden',
            'type'           => 'İşlem Türü',
            'id'             => 'Kayıt ID',
        ];

        $roleNames = [
            1 => 'Admin',
            2 => 'Depo Sorumlusu',
            3 => 'Görüntüleme',
        ];

        $output = [];

        foreach ($data as $key => $value) {
            // Ignore timestamps and remember_token in display
            if (in_array($key, ['created_at', 'updated_at', 'deleted_at', 'remember_token'])) {
                continue;
            }

            $label = $fieldLabels[$key] ?? ucfirst($key);

            // Format values nicely
            if ($key === 'is_active') {
                $formattedVal = $value ? 'AKTİF' : 'PASİF';
            } elseif ($key === 'role_id') {
                $formattedVal = $roleNames[$value] ?? "Rol #{$value}";
            } elseif ($key === 'password') {
                $formattedVal = '******** (Güncellendi)';
            } elseif (is_bool($value)) {
                $formattedVal = $value ? 'Evet' : 'Hayır';
            } elseif (is_null($value) || $value === '') {
                $formattedVal = 'Yok';
            } elseif (is_array($value)) {
                $formattedVal = json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $formattedVal = (string)$value;
            }

            $output[] = "<strong>{$label}:</strong> {$formattedVal}";
        }

        if (empty($output)) {
            return '-';
        }

        return implode(' &bull; ', $output);
    }
}
