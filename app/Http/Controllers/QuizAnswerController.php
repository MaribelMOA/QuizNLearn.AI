<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizAnswerRequest;
use App\Http\Requests\UpdateQuizAnswerRequest;
use App\Models\QuizAnswer;
use App\Models\QuizQuestion;
use Illuminate\Http\Response;

class QuizAnswerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(QuizAnswer::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuizAnswerRequest $request)
    {
        // Validar y almacenar la respuesta
        $validated = $request->validated();

        // Crear la respuesta para la pregunta
        $quizAnswer = QuizAnswer::create([
            'question_id' => $validated['question_id'],
            'answer_text' => $validated['answer_text'],
            'is_correct' => $validated['is_correct'],
            'explanation' => $validated['explanation'],
        ]);

        return response()->json($quizAnswer, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(QuizAnswer $quizAnswer)
    {
        return response()->json($quizAnswer);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuizAnswerRequest $request, QuizAnswer $quizAnswer)
    {
        $quizAnswer->update($request->validated());

        return response()->json($quizAnswer);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuizAnswer $quizAnswer)
    {
        $quizAnswer->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
