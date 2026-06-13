<?php

namespace App\Providers;

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
        view()->composer('*', function ($view) {
            $cartCount = 0;
            if (auth()->check()) {
                $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
            } else {
                $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
