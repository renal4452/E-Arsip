<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\Document;
use App\Observers\DocumentObserver;
use App\Models\User;                   
use App\Observers\UserObserver;        

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
        // 1. Keamanan HTTPS untuk Production
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // 2. NYALAKAN CCTV (Observer) UNTUK LOG AKTIVITAS
        Document::observe(DocumentObserver::class);
        User::observe(UserObserver::class); // <-- CCTV untuk perubahan password User!
    }
}