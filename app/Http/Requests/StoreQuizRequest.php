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
        $maxPdfs = $user->plan ? $user->plan->pdf_files : 1; // Definir un valor por defecto si no se encuentra el plan
        $maxUrls = $user->plan ? $user->plan->urls : 1; // Definir un valor por defecto si no se encuentra el plan
        $maxTextLimit = $user->plan ? $user->plan->text_limit : 0;

        return [
            'title' => 'required|string|max:150',
            'num_questions' => 'required|integer|min:1|max:' . $maxQuestions, // Validación dinámica.
            'difficulty_level' => 'required|in:Easy,Medium,Hard',
            'mode' => 'required|in:Quiz,Study,Arena',
            'pdfs' => ['nullable', 'array', 'max:' . $maxPdfs], // Validación de cantidad de PDFs
            'pdfs.*' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], // 5MB por archivo
            'urls' => ['nullable', 'array', 'max:' . $maxUrls], // Validación de cantidad de URLs
            'urls.*' => ['nullable', 'url'], // Validación de URLs individuales
            'manual_text' => ['nullable', 'string', 'max:' . $maxTextLimit],
            'topic' => ['nullable', 'string', 'max:255'],

           // 'quiz_data' => 'required|json', // El campo quiz_data debe ser un JSON válido

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasSource = $this->hasFile('pdfs') || $this->filled('urls') || $this->filled('manual_text');
            $hasTopic  = $this->filled('topic');

            if ($hasSource && $hasTopic) {
                $validator->errors()->add('topic', "You cant enter a topic if you've chosen a source.");
            }

            if (!$hasSource && !$hasTopic) {
                $validator->errors()->add('topic', 'You must wirte a topic or provide a source');
            }
        });
    }

}
