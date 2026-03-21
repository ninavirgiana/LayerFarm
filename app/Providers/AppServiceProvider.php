<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Carbon\Carbon;
use Illuminate\Support\Facades\URL;


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
        Paginator::useBootstrap();

        // SET BAHASA INDONESIA UNTUK TANGGAL
        Carbon::setLocale('id');

        // PAKSA HTTPS HANYA DI PRODUCTION
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
