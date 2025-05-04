<?php

namespace App\Http\Controllers;

use App\Events\CorrectAnswerShown;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Events\QuestionChanged;
use App\Http\Requests\StoreGameHistoryRequest;
use App\Http\Requests\UpdateGameHistoryRequest;
use App\Models\ArenaPlayer;
use App\Models\GameHistory;
use App\Models\Quiz;
use App\Services\PlayModesService;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


use Illuminate\Support\Str;
use App\Models\ArenaGame;
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
       // $arenaModeUses = $uses['arena_mode']['remaining'] ?? 6;

        $arenaModeUses=50;
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

        // Establecer el modo de juego en la sesión
        Session::put('game_mode', $mode);
        Session::put('current_quiz_id', $questionnaire->id);

        if ($mode === 'Study') {
            // Limpiar las preguntas previas de la sesión si existen
            Session::forget('study_mode.questions');
            Session::forget('study_mode.answers');
            Session::forget('study_mode.start_time');

            // Redirigir al método study, pasando el cuestionario como parámetro
            return redirect()->route('quizzes.study', ['quiz' => $questionnaire->id]);
        }

        if ($mode === 'Arena') {
            return $this->startArenaGame($questionnaire, $user);
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
            ->with('error', 'Invalid Game mode.');
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

        Session::forget('game_mode');
        Session::forget('current_quiz_id');

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

        Log::info('Modo estudio - siguiente pregunta', [
            'quiz_id' => $quiz->id,
            'next_question_id' => $nextQuestionId,
            'answered_count' => count($answered),
        ]);

        $startTime = Session::get('study_mode.start_time');
        $elapsedSeconds = now()->diffInSeconds($startTime);

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

       // Log::info("duratio:".$duration);
        // Calcula XP:  fórmula con base en total de preguntas y rapidez
        $totalQuestions = $quiz->num_questions; // o count(Session::get('study_mode.questions'))
        $timePenalty = max(1, $duration / 60); // evita división entre 0

        // XP = más preguntas en menos tiempo => más puntos
        $xpGained = round($totalQuestions * 2 / $timePenalty);
        // Borrar sesión
        Session::forget('study_mode.questions');
        Session::forget('study_mode.answers');
        Session::forget('study_mode.start_time');
        Session::forget('game_mode');
        Session::forget('current_quiz_id');


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

    public function exitMode(Quiz $quiz, Request $request)
    {
        // Obtener el modo de la sesión
        $mode = $request->input('mode') ?? Session::get('game_mode');

        // Verificar el progreso del usuario en el cuestionario
        $answers = Session::get('study_mode.answers', []);
        $answeredCount = count($answers);
        $minimumRequired = $quiz->num_questions/2; // Número mínimo de respuestas para guardar progreso

        if (in_array($mode, ['Study', 'Arena']) && $answeredCount >= $minimumRequired) {
            // Si se ha respondido el mínimo de preguntas, guardamos el tiempo y progreso
            $startTime = Session::get('study_mode.start_time');
            if ($startTime) {
                $duration = \Carbon\Carbon::parse($startTime)->diffInSeconds(now());
                GameHistory::create([
                    'user_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                    'mode' => $mode,
                    'total_time_seconds' => $duration,
                    'score' => 100, // O ajusta según lo que consideres
                ]);
            }
        }

        // Limpiar las sesiones al finalizar
        Session::forget('study_mode.questions');
        Session::forget('study_mode.answers');
        Session::forget('study_mode.start_time');

        Session::forget('game_mode');
        Session::forget('current_quiz_id');


        return redirect()->route('quizzes.index')->with('message', 'Game mode closed.');
    }

    /***
    ARENA GAME
     ***/

    public function startArenaGame(Quiz $quiz){
        $user = Auth::user();
        // Obtener el plan del usuario (host)


        // Verificar si ya existe un juego activo en sesión
        if (Session::has('arena_game_id')) {
            $arenaGameId = Session::get('arena_game_id');
            $arenaGame = ArenaGame::find($arenaGameId);

            if ($arenaGame && $arenaGame->status === 'active') {
                return view('quizzes.host-lobby', [
                    'quiz' => $quiz,
                    'quizId' => $quiz->id,
                    'pin' => $arenaGame->pin,
                    'arenaGameId' => $arenaGame->id,
                    'maxPlayers' => $this->getMaxPlayers($user),
                ]);
            }
        }


        // Crear registro en game_histories
        $history = GameHistory::create([
                    'user_id' => Auth::id(),
                    'quiz_id' => $quiz->id,
                    'mode' => 'Arena',
                    'total_time_seconds' => 0,
                    'score' => 100, // O ajusta según lo que consideres
                ]);

        // Generar PIN único
        $pin = Str::upper(Str::random(6));

        // Crear ArenaGame
        $arenaGame = ArenaGame::create([
            'game_history_id' => $history->id,
            'pin' => $pin,
            'start_time' => now(),
            'status' => 'active',
        ]);

        Session::put('arena_quiz_id', $quiz->id);
        Session::put('game_mode', 'Arena');
        Session::put('game_pin', $pin);
        Session::put('used_questions', []);
        Session::put('arena_game_id', $arenaGame->id); // 👉 Guardar ID en sesión
        Session::put("game.$arenaGame->id.current_question", 1);

        Log::info('Arena game id'.$arenaGame->id);
        return view('quizzes.host-lobby', ['quiz' => $quiz,
            'quizId' => $quiz->id,
            'pin' => $pin,
            'arenaGameId' => $arenaGame->id,
            'maxPlayers' =>  $this->getMaxPlayers($user),]);
//        return view('quizzes.host-lobby', [
//            'arenaGame' => $arenaGame
//            ]);
       // return redirect()->route('arena.host', ['arenaGame' => $arenaGame->id]);

    }

    private function getMaxPlayers($user)
    {
        $userPlan = $user->plan()->latest()->first();
        return $userPlan ? $userPlan->max_arena_players : 0;
    }

    public function showHostView($arenaGameId)
    {
        $arenaGame = ArenaGame::findOrFail($arenaGameId);  // Obtén el juego de la arena
        $quiz = $arenaGame->gameHistory->quiz;

        $players = ArenaPlayer::where('arena_game_id', $arenaGameId)->get();

        // Reiniciar respuestas de todos los jugadores
        foreach ($players as $player) {
            $player->has_responded = false;
            $player->save();
        }
        $questionNumber = Session::get("game.$arenaGameId.current_question", 1);
        $question = $quiz->quizQuestions[$questionNumber - 1];

        return view('quizzes.host-question', [
            'quiz' => $quiz,
            'question' => $question,
            'questionNumber' => $questionNumber,
            'totalQuestions' => $quiz->num_questions,
            'timeLimit' => 20, // segundos
            'playersAnswered' => Session::get("game.$arenaGame.players_answered", 0),
            'totalPlayers' => Session::get("game.$arenaGame.total_players", 1),
        ]);
    }
    public function nextQuestion($gameId)
    {
        $currentQuestion = Session::get("game.$gameId.current_question", 1);
        Session::put("game.$gameId.current_question", $currentQuestion + 1);

        // Lógica para continuar al siguiente jugador o siguiente pregunta
    }
    public function startGame($arenaGameId)
    {
        broadcast(new GameStarted($arenaGameId));
        Log::info("se supone que hice el broadcast");
       // return response()->json(['message' => 'Game started']);
    }



    //////////////////////////////////////////






    public function hostQuestion($quizId)
    {
        $quiz = Quiz::with('quizQuestions.quizQuestionAnswers')->findOrFail($quizId);
        $questions = $quiz->quizQuestions;

        if ($questions->isEmpty()) {
            return redirect()->route('quizzes.index')->with('error', 'No hay preguntas disponibles.');
        }

        // Obtener preguntas ya usadas desde sesión
        $usedQuestions = Session::get('used_questions', []);

        // Filtrar para obtener preguntas no usadas
        $availableQuestions = $questions->whereNotIn('id', $usedQuestions);

        if ($availableQuestions->isEmpty()) {
            return redirect()->route('quizzes.index')->with('error', 'Ya se usaron todas las preguntas.');
        }

        // Seleccionar pregunta al azar de las no usadas
        $question = $availableQuestions->random();

        // Guardar ID como ya usado
        Session::push('used_questions', $question->id);

        // Mapear respuestas con letras
        $letters = ['A', 'B', 'C', 'D', 'E', 'F'];
        $answers = $question->quizQuestionAnswers->values()->map(function ($a, $index) use ($letters) {
            return [
                'letter' => $letters[$index] ?? '?',
                'answer_text' => $a->answer_text
            ];
        });

        // Emitir evento
        event(new \App\Events\ArenaQuestionSent($quizId, [
            'question_text' => $question->question_text,
            'question_id' => $question->id,
            'answers' => $answers
        ]));

        return view('quizzes.host-question', compact('quiz'));
    }





    public function createArenaSession(Quiz $quiz)
    {
        Session::put('arena_mode.quiz_id', $quiz->id);
        Session::put('arena_mode.players', []); // array de jugadores
        Session::put('arena_mode.current_question_index', 0);
        Session::put('arena_mode.responses', []);

        return view('arena.host-lobby', compact('quiz'));
    }

    public function joinArena(Request $request)
    {
        $nickname = $request->input('nickname');
        $quizId = $request->input('quiz_id');

        $players = Session::get('arena_mode.players', []);
        $players[] = ['nickname' => $nickname, 'score' => 0];
        Session::put('arena_mode.players', $players);

        broadcast(new PlayerJoined($quizId, $nickname));

        return view('arena.waiting-room', compact('nickname', 'quizId'));
    }

    public function playArenaGame(Quiz $quiz)
    {
        $quiz->load('quizQuestions.quizQuestionAnswers', 'quizQuestions.type');

        broadcast(new GameStarted($quiz));

        return $this->nextArenaQuestion($quiz);
    }

    public function nextArenaQuestion(Quiz $quiz)
    {
        $index = Session::get('arena_mode.current_question_index', 0);
        $questions = $quiz->quizQuestions;

        if ($index >= $questions->count()) {
            return $this->finishArenaGame($quiz);
        }

        $question = $questions[$index];
        Session::put('arena_mode.current_question_index', $index + 1);

        broadcast(new QuestionChanged($quiz->id, $question));

        return view('arena.host-question', compact('question'));
    }

    public function submitArenaAnswer(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $questionId = $request->input('question_id');
        $nickname = $request->input('nickname');
        $answerId = $request->input('answer_id');

        $quiz = Quiz::with('quizQuestions.quizQuestionAnswers')->findOrFail($quizId);
        $question = $quiz->quizQuestions->firstWhere('id', $questionId);
        $correct = $question->quizQuestionAnswers->where('is_correct', true)->first();

        $isCorrect = $correct && $correct->id == $answerId;

        $responses = Session::get('arena_mode.responses', []);
        $responses[] = [
            'nickname' => $nickname,
            'question_id' => $questionId,
            'correct' => $isCorrect,
        ];
        Session::put('arena_mode.responses', $responses);

        broadcast(new CorrectAnswerShown($quiz->id, $questionId, $correct->id));

        return response()->json(['correct' => $isCorrect]);
    }

    public function finishArenaGame(Quiz $quiz)
    {
        $responses = Session::get('arena_mode.responses', []);
        $players = Session::get('arena_mode.players', []);

        // Contar respuestas correctas por jugador
        foreach ($players as &$player) {
            $player['score'] = collect($responses)
                    ->where('nickname', $player['nickname'])
                    ->where('correct', true)
                    ->count() * 100; // por ejemplo: 100 pts por acierto
        }

        // Ordenar por score
        $ranking = collect($players)->sortByDesc('score')->values()->all();

        broadcast(new GameFinished($quiz->id, $ranking));

        return view('arena.podium', compact('ranking'));
    }












}
