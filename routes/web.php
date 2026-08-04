<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- Read-Only Routes (Accessible to all logged-in users including Görüntüleme) ---
    
    // Products Read & Trash List
    Route::get('/products/trash', [ProductController::class, 'trash'])->name('products.trash');
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

    // Categories Read
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Warehouses Read
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
    Route::get('/warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');

    // Shelves Read
    Route::get('/shelves', [ShelfController::class, 'index'])->name('shelves.index');
    Route::get('/shelves/by-warehouse/{warehouseId}', [ShelfController::class, 'getByWarehouse'])->name('shelves.by-warehouse');

    // Stock Read & Movements
    Route::get('/stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('/stocks/critical', [StockController::class, 'critical'])->name('stocks.critical');
    Route::get('/stocks/movements', [StockController::class, 'movements'])->name('stocks.movements');

    // Reports Read & Export
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-csv', [ReportController::class, 'exportCsv'])->name('reports.export-csv');

    // --- Manager & Admin Mutation Routes (Role: Depo Sorumlusu, Admin) ---
    Route::middleware('role:depo sorumlusu,admin')->group(function () {
        
        // Products Mutations
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
        Route::post('/products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/empty-trash', [ProductController::class, 'emptyTrash'])->name('products.empty-trash');
        Route::delete('/products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');

        // Categories Mutations
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Warehouses Mutations
        Route::get('/warehouses/create', [WarehouseController::class, 'create'])->name('warehouses.create');
        Route::post('/warehouses', [WarehouseController::class, 'store'])->name('warehouses.store');
        Route::get('/warehouses/{warehouse}/edit', [WarehouseController::class, 'edit'])->name('warehouses.edit');
        Route::put('/warehouses/{warehouse}', [WarehouseController::class, 'update'])->name('warehouses.update');
        Route::delete('/warehouses/{warehouse}', [WarehouseController::class, 'destroy'])->name('warehouses.destroy');

        // Shelves Mutations
        Route::post('/shelves', [ShelfController::class, 'store'])->name('shelves.store');
        Route::put('/shelves/{shelf}', [ShelfController::class, 'update'])->name('shelves.update');
        Route::delete('/shelves/{shelf}', [ShelfController::class, 'destroy'])->name('shelves.destroy');

        // Stock Operations
        Route::get('/stocks/in', [StockController::class, 'createStockIn'])->name('stocks.in');
        Route::post('/stocks/in', [StockController::class, 'storeStockIn'])->name('stocks.in.store');

        Route::get('/stocks/out', [StockController::class, 'createStockOut'])->name('stocks.out');
        Route::post('/stocks/out', [StockController::class, 'storeStockOut'])->name('stocks.out.store');

        Route::get('/stocks/transfer', [StockController::class, 'createTransfer'])->name('stocks.transfer');
        Route::post('/stocks/transfer', [StockController::class, 'storeTransfer'])->name('stocks.transfer.store');
    });

    // --- Admin Only Management (Users & Audit Logs) ---
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });
});

require __DIR__.'/auth.php';
