<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cart = null;

            if (Auth::check()) {
                $cart = Cart::where('user_id', Auth::id())->first();
            } elseif (session()->has('cart_session_id')) {
                $cart = Cart::where('session_id', session('cart_session_id'))->whereNull('user_id')->first();
            }

            if ($cart) {
                $cart->touchLastActive();
            }

            $view->with('cartCount', $cart ? $cart->items()->sum('quantity') : 0);

            $wishlistCount = 0;
            if (Auth::check()) {
                $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
            }
            $view->with('wishlistCount', $wishlistCount);

            $pointsBalance = Auth::check() ? Auth::user()->loyalty_points : 0;
            $view->with('pointsBalance', $pointsBalance);
        });
    }
}
