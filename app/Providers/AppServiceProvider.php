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
            // Avoid running on exception renderer views to prevent infinite loops during errors
            if (str_starts_with($view->getName(), 'laravel-exceptions-renderer::')) {
                return;
            }

            $cartCount = 0;
            try {
                if (request()->hasSession()) {
                    if (auth()->check()) {
                        $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity');
                    } else {
                        $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                    }
                }
            } catch (\Exception $e) {
                // Fail gracefully if session or auth is not initialized
            }
            $view->with('cartCount', $cartCount);
        });
    }
}
