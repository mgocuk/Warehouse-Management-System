<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCodeHistory extends Model
{
    protected $fillable = [
        'product_id',
        'old_code',
        'new_code',
        'reason',
        'changed_by', // DB column is 'changed_by', not 'user_id'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // 'user' relation mapped to the 'changed_by' FK column
    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}