<?php

namespace App\Providers;

use App\Mail\UserCreate;
use App\Mail\UserMailChanged;
use App\Product;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Product::updated(function ($product) {
            if($product->quantity == 0 && $product->isAvailable()) {
                $product->status = Product::UNAVAILABLE_PRODUCT;
                $product->save();
            }
        });

        User::created(function ($user) {
            retry(10, function () use ($user) {
                Mail::to($user)->send(new UserCreate($user));
            }, 200);
        });
        User::updated(function ($user) {
            if($user->isDirty('email')) {
                retry(10, function () use ($user) {
                    Mail::to($user)->send(new UserMailChanged($user));
                }, 200);
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
