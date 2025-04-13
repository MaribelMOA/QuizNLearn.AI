<?php
namespace App\Services;

use App\Models\Feature;
use App\Models\FeatureType;
use Illuminate\Support\Facades\DB;

class UsageService
{
    public static function getUserPlan($userId)
    {
        // Obtener el plan del usuario
        return DB::table('user_plans')
            ->where('user_id', $userId)
            ->where('end_date', '>=', now())
            ->first();
    }

    public static function calculateAvailableUses($userId)
    {
        // Obtener el plan actual del usuario
        $userPlan = UsageService::getUserPlan($userId);
        if (!$userPlan) return null;

        // Obtener los detalles del plan
        $plan = DB::table('plans')->where('id', $userPlan->plan_id)->first();
        if (!$plan) return null;

        // Definir las funcionalidades a evaluar
        $featuresToTrack = [
            'study_mode' => [
                'plan_column' => 'study_mode_uses',
                'usage_table' => 'game_histories',
                'usage_column' => 'mode',
                'usage_value' => 'Study',
            ],
            'arena_mode' => [
                'plan_column' => 'arena_mode_uses',
                'usage_table' => 'game_histories',
                'usage_column' => 'mode',
                'usage_value' => 'Arena',
            ],
            'quiz_creation' => [
                'plan_column' => 'quizzes_allowed',
                'usage_table' => 'quizzes',
                'usage_column' => 'user_id',
            ],
            'summary_creation' => [
                'plan_column' => 'summaries_allowed',
                'usage_table' => 'summaries',
                'usage_column' => 'user_id',
            ],
        ];

        $results = [];

        foreach ($featuresToTrack as $code => $config) {
            // 1. Obtener el ID del tipo de feature
            $featureType = FeatureType::where('code', $code)->first();
            if (!$featureType) continue;

            // 2. Obtener todos los IDs de features que correspondan a ese tipo
            $featureIds = Feature::where('feature_type_id', $featureType->id)->pluck('id');

            // 3. Calcular usos realizados en el periodo del plan
            $used = DB::table($config['usage_table'])
                ->whereBetween('created_at', [$userPlan->start_date, $userPlan->end_date]);

            if (isset($config['usage_value'])) {
                $used->where($config['usage_column'], $config['usage_value']);
                $used->where('user_id', $userId);
            } else {
                $used->where($config['usage_column'], $userId);
            }

            $usedCount = $used->count();

            // 4. Calcular usos extra comprados por el usuario
            $extraCount = DB::table('feature_transactions')
                ->where('user_id', $userId)
                ->whereIn('feature_id', $featureIds)
                ->whereBetween('created_at', [$userPlan->start_date, $userPlan->end_date])
                ->sum('quantity');

            // 5. Total permitido (plan + extras)
            $baseLimit = $plan->{$config['plan_column']} ?? 0;
            $remaining = max(0, $baseLimit + $extraCount - $usedCount);

            // 6. Guardar resultados
            $results[$code] = [
                'plan_limit' => $baseLimit,
                'extra' => $extraCount,
                'used' => $usedCount,
                'remaining' => $remaining,
                'total_available' => $baseLimit + $extraCount,
            ];
        }

        return $results;
    }


    public  static function renewUserPlan($userId)
    {
        // Lógica para renovar el plan, puede ser simplemente actualizar las fechas del plan
        DB::table('user_plan')
            ->where('user_id', $userId)
            ->update([
                'start_date' => now(),
                'end_date' => now()->addMonth(),
            ]);
    }

//[
//'study_mode' => [
//'plan_limit' => 20,
//'extra' => 3,
//'used' => 15,
//'remaining' => 8,
//'total_available' => 23,
//],
//'quiz_creation' => [
//'plan_limit' => 10,
//'extra' => 2,
//'used' => 9,
//'remaining' => 3,
//'total_available' => 12,
//],
//...
//]

}

