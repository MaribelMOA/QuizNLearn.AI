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
                'description' => 'Permite crear nuevos quizzes con XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'study_mode',
                'name' => 'Study Mode',
                'description' => 'Uso del modo de estudio por XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'arena_mode',
                'name' => 'Arena Mode',
                'description' => 'Acceso al modo arena con XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'summary_creation',
                'name' => 'Summary Creation',
                'description' => 'Generación de resúmenes por XP.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
