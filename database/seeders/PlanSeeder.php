<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::insert([
            [
                'name' => 'Gratuito',
                'price' => 0,
                // Límite de preguntas
                'description' => 'PDFs (1 archivo 2MB), URLs (1), Texto (1,000 palabras), 10 preguntas máx.',
                'duration_months' => 0,
                'summaries_allowed' => 0,
                'quizzes_allowed' => 15,
                'max_questions' => 10,
                'study_mode_uses' => 10,
                'arena_mode_uses' => 3,
                'max_arena_players' => 0,
                'pdf_files' => 1, // Número de archivos PDF
                'urls' => 1, // Número de URLs
                'text_limit' => 1000, // Límite de palabras en texto
                'questions_limit' => 10,
            ],
            [
                'name' => 'Básico',
                'price' => 10,
                'description' => 'PDFs (3 archivos 5MB), URLs (3), Texto (2,500 palabras), 25 preguntas máx.',
                'duration_months' => 1,
                'summaries_allowed' => 10,
                'quizzes_allowed' => 50,
                'max_questions' => 25,
                'study_mode_uses' => 40,
                'arena_mode_uses' => 15,
                'max_arena_players' => 0,
                'pdf_files' => 3, // Número de archivos PDF
                'urls' => 3, // Número de URLs
                'text_limit' => 2500, // Límite de palabras en texto
                'questions_limit' => 25,
            ],
            [
                'name' => 'Premium',
                'price' => 30,
                'description' => 'PDFs (10 archivos 10MB), URLs (10), Texto (5,000 palabras), 50 preguntas máx.',
                'duration_months' => 1,
                'summaries_allowed' => 30,
                'quizzes_allowed' => 150,
                'max_questions' => 50,
                'study_mode_uses' => 120,
                'arena_mode_uses' => 50,
                'max_arena_players' => 0,
                'pdf_files' => 10, // Número de archivos PDF
                'urls' => 10, // Número de URLs
                'text_limit' => 5000, // Límite de palabras en texto
                'questions_limit' => 50,
            ],
        ]);
    }
}
