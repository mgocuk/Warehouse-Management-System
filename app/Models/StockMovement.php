<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'warehouse_id',
        'shelf_id',
        'user_id',
        'type',
        'old_quantity',
        'new_quantity',
        'change_amount',
        'reason',
    ];

    public function product()
    {
        // product_id can be null after a product is permanently deleted
        return $this->belongsTo(Product::class)->withDefault(['name' => 'Silinmiş Ürün', 'product_code' => '-']);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class)->withDefault(['name' => '-']);
    }

    public function shelf()
    {
        return $this->belongsTo(Shelf::class)->withDefault(['name' => '-']);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'Sistem']);
    }
}
