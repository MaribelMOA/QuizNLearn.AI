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
use Illuminate\Support\Facades\Session;
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

        if ($mode === 'Study') {
            // Limpiar las preguntas previas de la sesión si existen
            Session::forget('study_mode.questions');
            Session::forget('study_mode.answers');
            Session::forget('study_mode.start_time');

            // Redirigir al método study, pasando el cuestionario como parámetro
            return redirect()->route('quizzes.study', ['quiz' => $questionnaire->id]);
        }

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


    /****
     * STUDY MODE
    **/
    public function study(Quiz $quiz, Request $request)
    {
        $user = Auth::user();
        $uses = UsageService::calculateAvailableUses($user->id);

        $studyModeUses = $uses['study_mode']['remaining'] ?? 5;

        if ($studyModeUses <= 0) {
            return redirect()->route('quizzes.index')
                ->with('error', 'No tienes usos de Modo Estudio disponibles.');
        }

        // Cargar relaciones necesarias
        $quiz->load('quizQuestions.quizQuestionAnswers', 'quizQuestions.type');

        if ($quiz->quizQuestions->isEmpty()) {
            return redirect()->route('quizzes.index')
                ->with('error', 'Este cuestionario no tiene preguntas.');
        }

        // Iniciar sesión si aún no está iniciada
        if (!Session::has('study_mode.questions')) {
            $shuffled = $quiz->quizQuestions->pluck('id')->shuffle()->toArray();
            Session::put('study_mode.questions', $shuffled);
            Session::put('study_mode.answers', []);
            Session::put('study_mode.start_time', now());
        }

        $questionIds = Session::get('study_mode.questions', []);
        $answered = Session::get('study_mode.answers', []);

        // Verificar cuáles no han sido respondidas correctamente
        $remaining = array_filter($questionIds, function ($id) use ($answered) {
            foreach ($answered as $a) {
                if ($a['question_id'] == $id && $a['correct']) {
                    return false;
                }
            }
            return true;
        });

        if (empty($remaining)) {
            return redirect()->route('quizzes.study.finish', $quiz->id);
        }
        // Seleccionamos aleatoriamente una de las restantes
        $nextQuestionId = collect($remaining)->random();

        $nextQuestion = $quiz->quizQuestions->firstWhere('id', $nextQuestionId);


//        // Buscar la siguiente pregunta no contestada
//        $nextQuestionId = null;
//        foreach ($questionIds as $qid) {
//            if (!array_key_exists($qid, $answered)) {
//                $nextQuestionId = $qid;
//                break;
//            }
//        }
//
//        if (!$nextQuestionId) {
//            // Ya se contestaron todas las preguntas
//            return redirect()->route('quizzes.study.finish', $quiz->id);
//        }
//
//        // Buscar la pregunta por ID
//        $nextQuestion = $quiz->quizQuestions->firstWhere('id', $nextQuestionId);

        Log::info('Modo estudio - siguiente pregunta', [
            'quiz_id' => $quiz->id,
            'next_question_id' => $nextQuestionId,
            'answered_count' => count($answered),
        ]);

        $startTime = Session::get('study_mode.start_time');
        $elapsedSeconds = now()->diffInSeconds($startTime);

//        $isLast = count(array_filter($questionIds, function ($id) use ($answered) {
//                return !array_key_exists($id, $answered);
//            })) === 1;


        return view('quizzes.study-play', [
            'quiz' => $quiz,
            'question' => $nextQuestion,
            'answeredQuestions' => count($answered),
            'totalQuestions' => $quiz->num_questions,
            'elapsedSeconds' => $elapsedSeconds,
            'isLastQuestion' => count($remaining) === 1,

        ]);
    }


    public function submitStudyAnswer(Quiz $quiz, Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'answer' => 'required',
        ]);

        $questionId = $request->input('question_id');
        $answer = $request->input('answer');
        $elapsedTime = $request->input('elapsed_time'); // Tiempo recibido


        $question = $quiz->quizQuestions->where('id', $questionId)->first();
        $correct = false;
        $feedback = '';

        if (!$question) {
            return response()->json(['error' => 'Pregunta inválida.'], 400);
        }

        if ($question->type->name === 'open_question') {
            $correctAnswer = $question->quizQuestionAnswers->first()?->answer_text ?? '';
            $aiResponse = PlayModesService::evaluateOpenQuestionWithAI($question->question_text, $answer, $correctAnswer);

            $correct = $aiResponse['correct'];
            $feedback = $aiResponse['feedback'];
            if ($correct) {
                $feedback = 'Correct! ' . ($feedback ?? '');
            } else {
                $feedback = 'Incorrect. ' . ($feedback ?? '');
            }
        } else {
            $correctAnswer = $question->quizQuestionAnswers->where('is_correct', true)->first();
            $correct = ($answer == $correctAnswer->id);
            if ($correct) {
                $feedback = 'Correct! ' . ($correctAnswer->explanation ?? '');
            } else {
                $feedback = 'Incorrect. ' . ($correctAnswer->explanation ?? '');
            }
        }

        // Guardar en sesión
        $currentAnswers = Session::get('study_mode.answers', []);
        $currentAnswers[] = [
            'question_id' => $questionId,
            'given_answer' => $answer,
            'correct' => $correct,
        ];
        Session::put('study_mode.answers', $currentAnswers);

        // Manejar si la pregunta fue incorrecta: volver a agregarla
        if ($correct) {
            // Eliminar pregunta contestada correctamente
            $questions = Session::get('study_mode.questions', []);
            $questions = array_filter($questions, function ($id) use ($questionId) {
                return $id != $questionId;
            });
            Session::put('study_mode.questions', $questions);
        }


        return response()->json([
            'success' => true,
            'correct' => $correct,
            'feedback' => $feedback,
        ]);
    }

    public function finishStudyMode(Quiz $quiz, Request $request)
    {
        $startTime = Session::get('study_mode.start_time');
        $duration = \Carbon\Carbon::parse($startTime)->diffInSeconds(now());

        Log::info("duratio:".$duration);
        // Calcula XP:  fórmula con base en total de preguntas y rapidez
        $totalQuestions = $quiz->num_questions; // o count(Session::get('study_mode.questions'))
        $timePenalty = max(1, $duration / 60); // evita división entre 0

        // XP = más preguntas en menos tiempo => más puntos
        $xpGained = round($totalQuestions * 2 / $timePenalty);
        // Borrar sesión
        Session::forget('study_mode.questions');
        Session::forget('study_mode.answers');
        Session::forget('study_mode.start_time');

        // 👉 Guardar en GameHistory
        $user = Auth::user();
        $score = $xpGained; // o usa otra lógica si tienes un score separado
        $mode = 'Study'; // o usa el valor real si lo pasas por otro lado

        $gameHistory = GameHistory::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'mode' => $mode,
            'total_time_seconds' => $duration,
            'score' => 100,
        ]);

        return view('quizzes.study-finished', [
            'quiz' => $quiz,
            'totalTimeSeconds' => $duration,
            'xpGained' => $xpGained,
        ]);
    }


    /////////////////////
    ///
//    public function study(Quiz $quiz, Request $request)
//    {
//        $user = Auth::user();
//        $uses = UsageService::calculateAvailableUses($user->id);
//
//        $studyModeUses = $uses['study_mode']['remaining'] ?? 5;
//
//        if ($studyModeUses <= 0) {
//            return redirect()->route('quizzes.index')
//                ->with('error', 'No tienes usos de Modo Estudio disponibles.');
//        }
//
//        // Cargar relaciones necesarias
//        $quiz->load('quizQuestions.quizQuestionAnswers', 'quizQuestions.type');
//
//        if ($quiz->quizQuestions->isEmpty()) {
//            return redirect()->route('quizzes.index')
//                ->with('error', 'Este cuestionario no tiene preguntas.');
//        }
//
//        // Iniciar sesión si aún no está iniciada
//        if (!Session::has('study_mode.questions')) {
//            $shuffled = $quiz->quizQuestions->pluck('id')->shuffle()->toArray();
//            Session::put('study_mode.questions', $shuffled);
//            Session::put('study_mode.answers', []);
//        }
//
//        $questionIds = Session::get('study_mode.questions', []);
//        $answered = Session::get('study_mode.answers', []);
//
//        // Verificar cuáles no han sido respondidas correctamente
//        $remaining = array_filter($questionIds, function ($id) use ($answered) {
//            foreach ($answered as $a) {
//                if ($a['question_id'] == $id && $a['correct']) {
//                    return false;
//                }
//            }
//            return true;
//        });
//
//        if (empty($remaining)) {
//            return redirect()->route('quizzes.study.finish', $quiz->id);
//        }
//        // Seleccionamos aleatoriamente una de las restantes
//        $nextQuestionId = collect($remaining)->random();
//
//        $nextQuestion = $quiz->quizQuestions->firstWhere('id', $nextQuestionId);
//
//        Log::info('Modo estudio - siguiente pregunta', [
//            'quiz_id' => $quiz->id,
//            'next_question_id' => $nextQuestionId,
//            'answered_count' => count($answered),
//        ]);
//
//
//        return view('quizzes.study-play', [
//            'quiz' => $quiz,
//            'question' => $nextQuestion,
//            'answeredQuestions' => count($answered),
//            'totalQuestions' => $quiz->num_questions,
//            'isLastQuestion' => count($remaining) === 1,
//
//        ]);
//    }
//
//
//    public function submitStudyAnswer(Quiz $quiz, Request $request)
//    {
//        $request->validate([
//            'question_id' => 'required|exists:quiz_questions,id',
//            'answer' => 'required',
//            'elapsed_time' => 'required|integer',
//        ]);
//
//        $questionId = $request->input('question_id');
//        $answer = $request->input('answer');
//
//        $question = $quiz->quizQuestions->where('id', $questionId)->first();
//        $correct = false;
//        $feedback = '';
//
//        if (!$question) {
//            return response()->json(['error' => 'Pregunta inválida.'], 400);
//        }
//
//        if ($question->type->name === 'open_question') {
//            $correctAnswer = $question->quizQuestionAnswers->first()?->answer_text ?? '';
//            $aiResponse = PlayModesService::evaluateOpenQuestionWithAI($question->question_text, $answer, $correctAnswer);
//
//            $correct = $aiResponse['correct'];
//            $feedback = $aiResponse['feedback'];
//            if ($correct) {
//                $feedback = 'Correct! ' . ($feedback ?? '');
//            } else {
//                $feedback = 'Incorrect. ' . ($feedback ?? '');
//            }
//        } else {
//            $correctAnswer = $question->quizQuestionAnswers->where('is_correct', true)->first();
//            $correct = ($answer == $correctAnswer->id);
//            if ($correct) {
//                $feedback = 'Correct! ' . ($correctAnswer->explanation ?? '');
//            } else {
//                $feedback = 'Incorrect. ' . ($correctAnswer->explanation ?? '');
//            }
//        }
//
//        // Guardar en sesión
//        $currentAnswers = Session::get('study_mode.answers', []);
//        $currentAnswers[] = [
//            'question_id' => $questionId,
//            'given_answer' => $answer,
//            'correct' => $correct,
//        ];
//        Session::put('study_mode.answers', $currentAnswers);
//
//        // Manejar si la pregunta fue incorrecta: volver a agregarla
//        if ($correct) {
//            // Eliminar pregunta contestada correctamente
//            $questions = Session::get('study_mode.questions', []);
//            $questions = array_filter($questions, function ($id) use ($questionId) {
//                return $id != $questionId;
//            });
//            Session::put('study_mode.questions', $questions);
//        }
//
//
//        return response()->json([
//            'success' => true,
//            'correct' => $correct,
//            'feedback' => $feedback,
//        ]);
//    }
//
//    public function finishStudyMode(Quiz $quiz, Request $request)
//    {
//
//        // Calcula XP:  fórmula con base en total de preguntas y rapidez
//        $totalQuestions = $quiz->num_questions; // o count(Session::get('study_mode.questions'))
//
//        // XP = más preguntas en menos tiempo => más puntos
//        $xpGained = round($totalQuestions * 2);
//        // Borrar sesión
//        Session::forget('study_mode.questions');
//        Session::forget('study_mode.answers');
//
//        // 👉 Guardar en GameHistory
//        $user = Auth::user();
//        $score = $xpGained; // o usa otra lógica si tienes un score separado
//        $mode = 'Study'; // o usa el valor real si lo pasas por otro lado
////
////        $gameHistory = GameHistory::create([
////            'user_id' => $user->id,
////            'quiz_id' => $quiz->id,
////            'mode' => $mode,
////            'score' => 100,
////        ]);
//
//        return view('quizzes.study-finished', [
//            'quiz' => $quiz,
//            'xpGained' => $xpGained,
//        ]);
//    }



}
