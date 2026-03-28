<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = auth()->user(); // Obtener el usuario autenticado.

        // Obtener el número máximo de preguntas basado en el plan del usuario.
        $maxQuestions = $user->plan ? $user->plan->max_questions : 10; // Valor por defecto es 10 si no hay plan asociado.

        return [
           'title' => 'sometimes|string|max:150',
            'num_questions' => 'sometimes|integer|min:1|max:' . $maxQuestions, // Validación dinámica.
           'difficulty_level' => 'sometimes|in:Easy,Medium,Hard',
            'mode' => 'sometimes|in:Quiz,Study,Arena',
            'quiz_data' => 'sometimes|json', // El campo quiz_data debe ser un JSON válido
        ];
    }
}
