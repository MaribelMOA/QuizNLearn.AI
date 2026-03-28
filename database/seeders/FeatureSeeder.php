<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature;
use App\Models\FeatureType;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            ['code' => 'quiz_creation', 'name' => '+1 Quiz Creation', 'xp_price' => 100],
            ['code' => 'study_mode', 'name' => '+1 Study Mode Use', 'xp_price' => 150],
            ['code' => 'arena_mode', 'name' => '+1 Arena Mode Use', 'xp_price' => 150],
            ['code' => 'summary_creation', 'name' => '+1 Summary Creation', 'xp_price' => 400],
        ];

        foreach ($features as $data) {
            $type = FeatureType::where('code', $data['code'])->first();

            if ($type) {
                Feature::create([
                    'feature_type_id' => $type->id,
                    'name' => $data['name'],
                    'description' => null,
                    'xp_price' => $data['xp_price'],
                ]);
            }
        }
    }
}
