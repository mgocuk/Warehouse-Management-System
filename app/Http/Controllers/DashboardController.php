<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalStockQuantity = Stock::sum('quantity');
        $totalWarehouses = Warehouse::count();
        $totalShelves = Shelf::count();
        $todayMovementsCount = StockMovement::whereDate('created_at', today())->count();

        // Calculate products where total stock is below or equal to minimum stock
        $productsWithStock = Product::withSum('stocks', 'quantity')->get();
        $criticalProducts = $productsWithStock->filter(function ($product) {
            $totalStock = $product->stocks_sum_quantity ?? 0;
            return $totalStock <= $product->minimum_stock;
        });
        $criticalStockCount = $criticalProducts->count();

        $recentMovements = StockMovement::with(['product', 'user', 'warehouse', 'shelf'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalProducts',
            'totalStockQuantity',
            'totalWarehouses',
            'totalShelves',
            'todayMovementsCount',
            'criticalStockCount',
            'criticalProducts',
            'recentMovements'
        ));
    }
}
