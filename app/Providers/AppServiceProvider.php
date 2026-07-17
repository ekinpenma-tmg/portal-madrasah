<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

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
        // Portal Madrasah sepenuhnya berbahasa Indonesia — paksa locale Indonesia
        // di sini (tidak bergantung ke .env) supaya semua translatedFormat()
        // (nama bulan, nama hari, dsb di seluruh aplikasi) konsisten Indonesia,
        // termasuk saat dirender lewat DomPDF.
        App::setLocale('id');
        Carbon::setLocale('id');

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
