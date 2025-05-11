<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSummaryRequest extends FormRequest
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

            'pdf_file' => ['nullable', 'array', 'max:' . $maxPdfs], // Validación de cantidad de PDFs
            'pdf_file.*' => ['nullable', 'mimes:pdf', 'max:5120'], // 5MB por archivo
            'urls' => ['nullable', 'array', 'max:' . $maxUrls], // Validación de cantidad de URLs
            'urls.*' => ['nullable', 'url'], // Validación de URLs individuales
            'manual_text' => ['nullable', 'string', 'max:' . $maxTextLimit],
            'topic' => ['nullable', 'string', 'max:255'],

        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (
                !$this->filled('manual_text') &&
                !$this->hasFile('pdfs') &&
                !$this->hasFile('topic') &&
                empty($this->input('urls'))
            ) {
                $validator->errors()->add('content', 'You must provide at least one content source: PDF, URL, or manual text.');
            }
        });
    }
}
