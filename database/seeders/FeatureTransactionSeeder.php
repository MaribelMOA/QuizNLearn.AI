<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeatureTransaction;
use App\Models\User;
use App\Models\Feature;

class FeatureTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $features = Feature::all();

        if ($users->isEmpty() || $features->isEmpty()) {
            $this->command->warn('No hay usuarios o features para crear transacciones.');
            return;
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 3; $i++) {
                $feature = $features->random();

                FeatureTransaction::create([
                    'user_id' => $user->id,
                    'feature_id' => $feature->id,
                    'quantity' => rand(1, 5),
                    'price_xp' => rand(100, 500),
                ]);
            }
        }
    }
}
