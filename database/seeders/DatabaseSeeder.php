<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //User::factory()->count(5)->hasStores(rand(10, 20))->create();
        User::factory()->count(3)->has(
            Store::factory()->count(rand(3, 5))->has(
                Product::factory()->count(rand(10, 20))
            )
        )->create();
    }
}
