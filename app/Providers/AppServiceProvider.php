<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Use custom pagination view
        \Illuminate\Pagination\Paginator::defaultView('vendor.pagination.custom');

        // Share site settings with ALL views
        try {
            if (Schema::hasTable('site_settings')) {
                $siteSettings = SiteSetting::pluck('value', 'key');
                View::share('siteSettings', $siteSettings);
            }
        } catch (\Exception $e) {
            View::share('siteSettings', collect());
        }
    }
}
