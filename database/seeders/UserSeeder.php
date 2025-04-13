<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = Plan::all();

        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "User $i",
                'email' => "user$i@example.com",
                'password' => Hash::make('password'),
                'customer_id' => 'sim_customer_' . $i,
                'payment_method_id' => collect(['stripe', 'paypal'])->random(),
                'current_plan_id' => $plans->isNotEmpty() ? $plans->random()->id : null,
                'xp' => rand(100, 1000),
            ]);
        }
    }
}
