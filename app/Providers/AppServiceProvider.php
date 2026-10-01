<?php

namespace App\Providers;

use App\Kroombase\Client;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Klien KroomBase dipakai bersama: konfigurasinya dibaca sekali dari
        // config/kroombase.php (yang bersumber dari .env).
        $this->app->singleton(Client::class, fn () => Client::fromConfig());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
