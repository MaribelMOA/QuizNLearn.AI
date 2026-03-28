<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;

class QuizAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las preguntas
        $questions = QuizQuestion::all();

        foreach ($questions as $question) {
            // Obtener el cuestionario relacionado con la pregunta
            $quiz = $question->quiz;
            $typeName = $question->type->name;

            // Validar si el cuestionario está en modo Arena
            if ($quiz->mode === 'Arena' && $typeName === 'Open Questions') {
                // Si el modo es Arena, no crear respuestas para preguntas abiertas
                continue;
            }

            // Crear respuestas según el tipo de pregunta
            if ($typeName === 'True or False') {
                // Dos opciones
                QuizAnswer::create([
                    'question_id' => $question->id,
                    'answer_text' => 'True',
                    'is_correct' => rand(0, 1), // Respuesta aleatoria
                    'explanation' => 'True is the correct answer based on logic.'
                ]);

                QuizAnswer::create([
                    'question_id' => $question->id,
                    'answer_text' => 'False',
                    'is_correct' => rand(0, 1), // Respuesta aleatoria
                    'explanation' => 'False is also a valid option.'
                ]);
            } elseif ($typeName === 'Multiple Choice') {
                // Cuatro opciones
                for ($i = 1; $i <= 4; $i++) {
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer_text' => 'Option ' . $i,
                        'is_correct' => $i === 1, // Solo la primera es correcta
                        'explanation' => 'Option ' . $i . ' explanation.'
                    ]);
                }
            } elseif ($typeName === 'Open Questions') {
                // Si no estamos en modo Arena, crear una respuesta para preguntas abiertas
                QuizAnswer::create([
                    'question_id' => $question->id,
                    'answer_text' => 'This is a sample open answer.',
                    'is_correct' => true,
                    'explanation' => 'Some explanation',
                ]);
            }
        }
    }
}
