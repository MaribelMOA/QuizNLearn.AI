<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPlan;
use Illuminate\Support\Carbon;
class UserPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Simular fechas de inicio y fin (último mes)
            $startDate = Carbon::now()->subDays(rand(10, 25));
            $endDate = (clone $startDate)->addMonths($user->currentPlan?->duration_months ?? 1);

            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $user->current_plan_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }
    }
}
