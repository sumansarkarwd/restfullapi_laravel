<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        \App\User::truncate();
        \App\Category::truncate();
        \App\Product::truncate();
        \App\Transaction::truncate();
        DB::table('category_product')->truncate();

        \App\User::flushEventListeners();
        \App\Category::flushEventListeners();
        \App\Product::flushEventListeners();
        \App\Transaction::flushEventListeners();

        $userQuantity = 1000;
        $categoryQuantity = 30;
        $productQuantity = 500;
        $transactionQuantity = 1000;

        factory(\App\User::class, $userQuantity)->create();
        factory(\App\Category::class, $categoryQuantity)->create();
        factory(\App\Product::class, $productQuantity)->create()->each(
            function ($product) {
                $categories = \App\Category::all()->random(mt_rand(1, 5))->pluck('id');
                $product->categories()->attach($categories);
            }
        );
        factory(\App\Transaction::class, $transactionQuantity)->create();

    }
}
