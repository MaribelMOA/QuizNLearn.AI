<?php

namespace Database\Seeders;

use App\Http\Controllers\XpPriceController;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

//        User::factory()->create([
//            'name' => 'Test User',
//            'email' => 'test@example.com',
//        ]);

        $this->call([

            PlanSeeder::class,
            FeatureTypeSeeder::class,
            FeatureSeeder::class,
            XpPriceSeeder::class,
            UserSeeder::class,
            UserPlanSeeder::class,
            FeatureTransactionSeeder::class,
            XpTransactionSeeder::class,

        ]);
    }
}
