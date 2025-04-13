<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
            'title' => 'required|string|max:150',
            'num_questions' => 'required|integer|min:1|max:' . $maxQuestions, // Validación dinámica.
            'difficulty_level' => 'required|in:Easy,Medium,Hard',
            'mode' => 'required|in:Quiz,Study,Arena',
            'quiz_data' => 'required|json', // El campo quiz_data debe ser un JSON válido
        ];
    }
}
