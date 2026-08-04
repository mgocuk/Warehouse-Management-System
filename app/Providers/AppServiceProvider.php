<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shelf;
use App\Models\Stock;
use App\Models\User;
use App\Models\Warehouse;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(AuditObserver::class);
        Stock::observe(AuditObserver::class);
        Warehouse::observe(AuditObserver::class);
        Shelf::observe(AuditObserver::class);
        Category::observe(AuditObserver::class);
        // Register User observer to track privilege changes (role changes, activation/deactivation)
        User::observe(AuditObserver::class);
    }
}
