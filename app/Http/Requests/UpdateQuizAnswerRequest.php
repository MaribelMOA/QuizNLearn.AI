<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizAnswerRequest extends FormRequest
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
        return [
            'question_id' => 'sometimes|exists:quiz_questions,id', // Aseguramos que la pregunta existe
            'answer_text' => 'sometimes|string', // Validación del texto de la respuesta
            'is_correct' => 'sometimes|boolean', // Validación de si es correcta o no
            'explanation' => 'nullable|string', // La explicación es opcional

        ];
    }
}
