<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Observers\ProductObserver;
use App\Observers\ProductVariantObserver;
use App\Observers\VendorObserver;
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
        \App\Models\Product::observe(ProductObserver::class);
         \App\Models\ProductVariant::observe(ProductVariantObserver::class);
         \App\Models\Vendor::observe(VendorObserver::class);
    }
}
