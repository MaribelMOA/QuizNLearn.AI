<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameHistoryRequest;
use App\Http\Requests\UpdateGameHistoryRequest;
use App\Models\GameHistory;
use App\Models\Quiz;
use App\Services\PlayModesService;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
class GameHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(GameHistory::with(['user', 'quiz'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameHistoryRequest $request)
    {
        $gameHistory = GameHistory::create($request->validated());
        return response()->json($gameHistory, Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     */
    public function show(GameHistory $gameHistory)
    {
        return response()->json($gameHistory->load(['user', 'quiz']));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameHistoryRequest $request, GameHistory $gameHistory)
    {
        $gameHistory->update($request->validated());
        return response()->json($gameHistory);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameHistory $gameHistory)
    {
        $gameHistory->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);

    }

    public function play(Quiz $questionnaire, Request $request)
    {
        $mode = $request->query('mode'); // Obtener 'mode' de la query string

        $user = Auth::user();
        $uses = UsageService::calculateAvailableUses($user->id);

        $studyModeUses = $uses['study_mode']['remaining'] ?? 5;
        $arenaModeUses = $uses['arena_mode']['remaining'] ?? 6;

        // Verificar si el usuario tiene usos disponibles para el modo seleccionado
        if ($mode === 'Study' && $studyModeUses <= 0) {
            return redirect()->route('quizzes.index')
                ->with('error', 'No tienes usos de Modo Estudio disponibles.');
        }

        if ($mode === 'Arena' && $arenaModeUses <= 0) {
            return redirect()->route('quizzes.index')
                ->with('error', 'No tienes usos de Modo Arena disponibles.');
        }

        // Cargar las relaciones necesarias
        $questionnaire->load('quizQuestions.quizQuestionAnswers', 'quizQuestions.type');

        if ($mode === 'Quiz') {
//            return view('layouts.play', [
//                'quiz' => $questionnaire,
//                'questions' => $questionnaire->quizQuestions,
//                'answeredQuestions' => 0,
//                'totalQuestions' => $questionnaire->num_questions,
//                'mode' => $mode,
//            ]);
            return view('quizzes.quiz-play', [
                'quiz' => $questionnaire,
                'questions' => $questionnaire->quizQuestions,
                'answeredQuestions' => 0,
                'totalQuestions' => $questionnaire->num_questions,
                'mode' => $mode,
            ]);
        }

        // Si llega un modo desconocido
        return redirect()->route('quizzes.index')
            ->with('error', 'Modo de juego no válido.');
    }

    public function submitQuizMode(Quiz $questionnaire, Request $request)
    {
        $mode = 'Quiz';//$request->query('mode');
        $user = Auth::user();


        // Obtener las respuestas del usuario
        $answers = $request->input('answers'); // Es el array de respuestas que se enviarán al servidor

        // Inicializar variables para el puntaje
        $correctAnswersCount = 0;
        $totalQuestions = $questionnaire->num_questions;
        $aiValidations = [];

        // Verificar las respuestas y calcular el puntaje
        // Verificar las respuestas y calcular el puntaje
        foreach ($questionnaire->quizQuestions as $question) {
            // Obtener la respuesta enviada por el usuario
            $userAnswer = $answers[$question->id] ?? null;
            // Obtener la respuesta correcta de la base de datos
            $correctAnswer = $question->quizQuestionAnswers->where('is_correct', true)->first();

            if ($correctAnswer) {
                Log::info("Enteres correct anwser");
                // Si la respuesta es de texto (en lugar de opción múltiple), normalizamos y comparamos
                if ($question->type->name === 'open_question') {
                    Log::info("Open question detected. Verifying with AI...");
                    // Normalizamos las respuestas antes de compararlas
                    $normalizedUserAnswer = strtolower(trim($userAnswer));
                    $normalizedCorrectAnswer = strtolower(trim($correctAnswer->answer));

                    // Compara las respuestas normalizadas
                    if ($normalizedUserAnswer === $normalizedCorrectAnswer) {
                        $correctAnswersCount++;
                        $aiValidations[$question->id] = true;
                    } else {
                        Log::info("AI verification needed.");
                        // Verificamos con la IA
                        $isAIValid = PlayModesService::verifyOpenQuestionWithAI($question->question_text, $normalizedUserAnswer, $normalizedCorrectAnswer);

                        if ($isAIValid) {
                            $correctAnswersCount++;
                        }
                        $aiValidations[$question->id] = $isAIValid; // Guardamos el estado de la validación de IA
                    }
                } else {
                    // Opción múltiple o cualquier otro tipo de pregunta
                    if ($userAnswer == $correctAnswer->id) {
                        $correctAnswersCount++;
                        $aiValidations[$question->id] = true;
                    } else {
                        $aiValidations[$question->id] = false;
                    }
                }
            }
        }

        // Calcular el puntaje (por ejemplo, un puntaje de 100 por respuesta correcta)
        $score = $correctAnswersCount * 100 / $totalQuestions;
        $totalTimeSeconds=$request->input('total_time_seconds'); // Tiempo total

        // Registrar el juego en la tabla game_histories
        $gameHistory = GameHistory::create([
            'user_id' => $user->id,
            'quiz_id' => $questionnaire->id,
            'mode' => $mode,
            'total_time_seconds' => $totalTimeSeconds,
            'score' => $score
        ]);

        // Sumar XP al usuario (supongamos que el usuario gana XP basado en el puntaje)
        $xpGained =2*$correctAnswersCount;// $score * 2; // Ejemplo de cálculo de XP
        $user->increment('xp', $xpGained);

        // Guardar los resultados en la sesión
//        session([
//            'quizResults' => [
//                'quiz' => $questionnaire,
//                'answers' => $answers,
//                'correctAnswersCount' => $correctAnswersCount,
//                'totalQuestions' => $totalQuestions,
//                'score' => $score,
//                'mode' => $mode,
//                'xpGained' => $xpGained,
//                'totalTimeSeconds' => $totalTimeSeconds,
//            ]
//        ]);

       // return view('quizzes.wait-results');
        return view('quizzes.quiz-mode-results',  [
            'quiz' => $questionnaire,
            'answers' => $answers,
            'correctAnswersCount' => $correctAnswersCount,
            'totalQuestions' => $totalQuestions,
            'score' => $score,
            'mode' => $mode,
            'xpGained' => $xpGained,
            'totalTimeSeconds' => $totalTimeSeconds,
            'aiValidations' => $aiValidations,
        ]);


//
//        // Redirigir a la vista de resultados
//        Log::info('HI Redirigiendo a la ruta de resultados');
//        return redirect()->route('quizzes.showQuizResults');

    }

    public function showQuizResults()
    {
        Log::info("Enteres sho quiz results");
        // Recuperar los resultados de la sesión
        $quizResults = session('quizResults');

        if (!$quizResults) {
            Log::warning("No hay resultados en sesión. Redirigiendo a quizzes.index");
            return redirect()->route('quizzes.index'); }

        return view('quizzes.quiz-mode-results', $quizResults);
    }


}
