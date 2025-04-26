<?php

namespace App\Http\Requests;

use App\Http\Controllers\UserPlanController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

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
        // Obtener el plan del usuario
        $userPlan = $user->userPlan; // Esto devolverá el único UserPlan asociado

// Si no existe un plan asociado, puedes utilizar valores por defecto
        $maxUrls = $userPlan ? $userPlan->plan->urls : 1;
        $maxQuestions = $userPlan ? $userPlan->plan->max_questions : 10;
        $maxPdfs = $userPlan ? $userPlan->plan->pdf_files : 1;
        $maxTextLimit = $userPlan ? $userPlan->plan->text_limit : 0;


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
            'question_types' => ['required', 'array'],
            'question_types.*' =>  ['in:multiple_choice,true_false,open_ended'],

            // 'quiz_data' => 'required|json', // El campo quiz_data debe ser un JSON válido

        ];
    }

//    public function withValidator($validator)
//    {
//        $validator->after(function ($validator) {
//            $hasSource = $this->hasFile('pdfs') || $this->filled('urls') || $this->filled('manual_text');
//            $hasTopic  = $this->filled('topic');
//
//            if ($hasSource && $hasTopic) {
//                $validator->errors()->add('topic', "You cant enter a topic if you've chosen a source.");
//            }
//
//            if (!$hasSource && !$hasTopic) {
//                $validator->errors()->add('topic', 'You must wirte a topic or provide a source');
//            }
//        });
//    }

}
