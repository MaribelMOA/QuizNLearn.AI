<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeatureType;
class FeatureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FeatureType::insert([
            [
                'code' => 'quiz_creation',
                'name' => 'Quiz Creation',
                'description' => 'Allows creating new quizzes using XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'study_mode',
                'name' => 'Study Mode',
                'description' => 'Use of study mode with XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'arena_mode',
                'name' => 'Arena Mode',
                'description' => 'Access to arena mode with XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'summary_creation',
                'name' => 'Summary Creation',
                'description' => 'Generation of summaries using XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
