<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuestionType;
use Illuminate\Support\Str;
class QuizQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los cuestionarios y tipos de preguntas
        $quizzes = Quiz::all();
        $questionTypes = QuestionType::all();

        // Recorrer cada cuestionario
        foreach ($quizzes as $quiz) {
            // Si el cuestionario es de tipo 'Arena', solo permitir preguntas que no sean 'Open Questions'
            if ($quiz->mode === 'Arena') {
                $questionTypes = $questionTypes->where('name', '!=', 'Open Questions');
            } else {
                // Si el cuestionario no es de tipo 'Arena', incluir 'Open Questions'
                // No es necesario filtrar, ya que las preguntas abiertas pueden ser incluidas
                $questionTypes = $questionTypes;
            }

            // Número de preguntas para este cuestionario
            $numQuestions = $quiz->num_questions;

            // Crear preguntas para el cuestionario
            for ($i = 0; $i < $numQuestions; $i++) {
                // Seleccionar un tipo de pregunta aleatorio de la lista filtrada
                $type = $questionTypes->random();

                // Crear la pregunta en la base de datos
                QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_type_id' => $type->id,
                    'question_text' => 'What is ' . Str::random(5) . '?',
                ]);
            }
        }
    }
}
