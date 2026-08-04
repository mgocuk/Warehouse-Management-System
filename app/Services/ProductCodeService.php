<?php

namespace App\Services;

use App\Models\Product;

class ProductCodeService
{
    public function generate($category): string
    {
        $prefix = $category->code;

        // Include soft-deleted products to avoid UNIQUE constraint violations
        $lastProduct = Product::withTrashed()
            ->where('product_code', 'LIKE', $prefix . '-%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastProduct) {
            // Dynamically calculate offset based on actual prefix length (prefix + '-')
            $offset = strlen($prefix) + 1;
            $lastNumber = intval(substr($lastProduct->product_code, $offset));
            $number = $lastNumber + 1;
        } else {
            $number = 1;
        }

        return $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}