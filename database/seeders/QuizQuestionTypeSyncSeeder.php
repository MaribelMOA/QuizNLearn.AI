<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\QuizQuestion;
use App\Models\Quiz;
use App\Models\QuestionType;

class QuizQuestionTypeSyncSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener las combinaciones de quiz_id y question_type_id
        $combinations = QuizQuestion::select('quiz_id', 'question_type_id')
            ->distinct()
            ->get();

        // Recorrer cada combinación
        foreach ($combinations as $combo) {
            // Obtener el modo del cuestionario
            $quiz = Quiz::find($combo->quiz_id);

            // Verificar si el modo es "Arena"
            if ($quiz && $quiz->mode === 'Arena') {
                // Si es "Arena", evitar insertar "Open Questions"
                // Verificar si la question_type_id es para "Open Question" (usualmente tiene un ID específico)
                $openQuestionTypeId = QuestionType::where('name', 'Open Questions')->first()->id;

                if ($combo->question_type_id === $openQuestionTypeId) {
                    // Si es una pregunta abierta, no hacer la inserción
                    continue;
                }
            }

            // Si no es Arena o la pregunta no es abierta, insertar o actualizar la relación
            DB::table('quiz_question_type')->updateOrInsert(
                [
                    'quiz_id' => $combo->quiz_id,
                    'question_type_id' => $combo->question_type_id,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
