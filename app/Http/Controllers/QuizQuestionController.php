<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizQuestionRequest;
use App\Http\Requests\UpdateQuizQuestionRequest;
use App\Models\QuizQuestion;
use App\Models\Quiz;
use Illuminate\Http\Response;

class QuizQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(QuizQuestion::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizQuestionRequest $request)
    {
        // Validar y almacenar la pregunta
        $validated = $request->validated();

        // Crear la pregunta para el quiz
        $quizQuestion = QuizQuestion::create([
            'quiz_id' => $validated['quiz_id'],
            'question_text' => $validated['question_text'],
        ]);

        return response()->json($quizQuestion, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizQuestion $quizQuestion)
    {
        return response()->json($quizQuestion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizQuestionRequest $request, QuizQuestion $quizQuestion)
    {
        $quizQuestion->update($request->validated());

        return response()->json($quizQuestion);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuizQuestion $quizQuestion)
    {
        $quizQuestion->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
