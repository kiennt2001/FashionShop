<?php

namespace App\Providers;

use App\Http\View\Composers\MenuComposer;
use Illuminate\Support\Facades;
use Illuminate\Support\ServiceProvider;
use App\Http\View\Composers\CartComposer;
use Illuminate\View\View;
 
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }
 
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Facades\View::composer('header', MenuComposer::class);
        Facades\View::composer('cart', CartComposer::class);
    }
}