<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'name',
        'location',
        'description',
    ];

    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }
}