<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use SoftDeletes;


    protected $fillable = [

        'product_code',
        'name',
        'category_id',
        'description',
        'unit',
        'minimum_stock'

    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function codeHistories()
{
    return $this->hasMany(ProductCodeHistory::class);
}

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

}