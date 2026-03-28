<?php

namespace App\Http\Controllers;

use App\Events\PlayerAnswered;
use App\Events\PlayerJoined;

use App\Http\Requests\StoreArenaGameRequest;
use App\Http\Requests\UpdateArenaGameRequest;
use App\Models\ArenaGame;
use App\Models\ArenaPlayer;
use App\Models\GameHistory;
use App\Models\QuizQuestion;
use App\Models\UserPlan;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\QuizAnswer;

use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ArenaGameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(ArenaGame::with('gameHistory')->get());

    }
    public function create()
    {
        $user = Auth::user();
        $userId = $user->id;
        $uses =UsageService::calculateAvailableUses($userId);
        $arenaModeUses = $uses['arena_mode']['remaining']?? 6;

        // Verificar si el usuario tiene usos disponibles
        if ($arenaModeUses <= 0) {
            return redirect()->route('quiz.index')
                ->with('error', 'No tienes usos de Modo Arena disponibles.');
        }

        // Obtener cuestionarios del usuario
        //ESTA MAL
//        $questionnaires = Quiz::where('user_id', $user->id)
//            ->withCount('quizQuestions')
//            ->having('num_questions', '>', 0)
//            ->get();
        $questionnaires = Quiz::where('user_id', $user->id)

            ->get();

        return view('arena.create', compact('questionnaires'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArenaGameRequest $request)
    {
        $arenaGame = ArenaGame::create($request->validated());
        return response()->json($arenaGame, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(ArenaGame $arenaGame)
    {
        return response()->json($arenaGame->load('gameHistory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArenaGameRequest $request, ArenaGame $arenaGame)
    {
        $arenaGame->update($request->validated());
        return response()->json($arenaGame);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArenaGame $arenaGame)
    {
        $arenaGame->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    ////////////////////////////
    public function chooseArenaQuiz(Request $request)
    {
        $user = Auth::user();

        // Obtener cuestionarios con filtros
        $query = Quiz::where('user_id', $user->id)
            ->where('mode', 'Arena');

        // Obtener cuestionarios, ordenados primero por el uso más reciente y luego por la fecha de creación
        $questionnaires = $query
            ->with(['questionTypes', 'quizQuestions', 'gameHistories'])
            ->withCount('quizQuestions')
            ->select('quizzes.*')
            ->selectSub(
                GameHistory::selectRaw('MAX(created_at)')
                    ->whereColumn('quiz_id', 'quizzes.id'),
                'last_game_history'
            )
            ->orderByRaw(
                'COALESCE((
            SELECT MAX(created_at)
            FROM game_histories
            WHERE game_histories.quiz_id = quizzes.id
        ), quizzes.created_at) DESC'
            )
            ->paginate(5);



        // Agregar la verificación de preguntas abiertas para cada cuestionario
        foreach ($questionnaires as $quiz) {
            $quiz->hasOpenQuestions = $quiz->questionTypes->contains(function($type) {
                return $type->name === 'open_question';  // Cambia 'Open' al nombre adecuado de tu tipo de pregunta
            });
        }

        // Obtener estadísticas del usuario
        $totalQuizzes = Quiz::where('user_id', $user->id)
            ->where('mode', 'Arena')->count();

        $userId = $user->id;
        $uses =UsageService::calculateAvailableUses($userId);
        // Estos valores vendrían de la suscripción del usuario o configuración
        $availableCreations = $uses['quiz_creation']['remaining']?? 4;

        $arenaModeUses = $uses['arena_mode']['remaining']?? 6;
//        $studyModeUses = 0;
             //$arenaModeUses = 50;
        //   $availableCreations=0;


        $currentUserPlan = UserPlan::where('user_id', $userId)
            ->where('end_date', '>=', now()) // Solo los planes activos
            ->latest('end_date') // El más reciente
            ->first();



        // Ahora obtenemos los datos del plan desde la tabla `plans` (suponiendo que tienes la relación 'plan')
        $plan = $currentUserPlan->plan; // Esto asume que tienes la relación 'plan' definida en tu modelo `UserPlan`

        // Extraer los límites del plan
        $planLimits = [
            'max_questions' => $plan->max_questions ?? 10,
            'pdf_files'     => $plan->pdf_files ?? 0,
            'urls'          => $plan->urls ?? 0,
            'text_limit'    => $plan->text_limit ?? 1000,
        ];


        return view('quizzes.choose-quiz', compact(
            'questionnaires',
            'totalQuizzes',
            'availableCreations',
            'arenaModeUses',
            'planLimits'
        ));
    }

    public function joinGame(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string|max:255',
            'pin' => 'required|string|size:6',
        ]);

        $arenaGame = ArenaGame::where('pin', strtoupper($request->pin))
            ->where('status', 'active')
            ->first();

        if (!$arenaGame) {
            return redirect()->back()->withErrors(['pin' => 'Invalid or inactive game PIN.']);
        }
        $userId = Auth::id();

        // 1. Verificar si ya está inscrito (autenticado)
        $existingPlayer = ArenaPlayer::where('arena_game_id', $arenaGame->id)
            ->where('user_id', $userId)
           // ->when(!$userId, fn($q) => $q->where('name', $request->player_name))
            ->first();

        if ($existingPlayer) {
            // Ya existe: guardar en sesión y redirigir
            session([
                'player_name' => $existingPlayer->name,
                'arena_player_id' => $existingPlayer->id,
                'arena_game_pin' => $arenaGame->pin,
                'arena_game_id' => $arenaGame->id,
            ]);

            return view('quizzes.waiting', [
                'player' => $existingPlayer,
                'arenaGameId' => $arenaGame->id,
                'pin' => $arenaGame->pin,
                'score' => $existingPlayer->score,
            ]);
        }

        $currentPlayersCount = ArenaPlayer::where('arena_game_id', $arenaGame->id)
            ->where('is_host', false)
            ->count();

        // Obtener el límite de jugadores desde el plan del host
        $userPlan = $arenaGame->gameHistory->user->plan;
        $maxPlayers = $userPlan ? $userPlan->max_arena_players : 0;

        if ($maxPlayers > 0 && $currentPlayersCount >= $maxPlayers) {
            // El juego está lleno, no permitir que más jugadores se unan
            return redirect()->back()->withErrors(['pin' => 'The game is full. No more players can join.']);
        }

        $host = ArenaPlayer::where('arena_game_id', $arenaGame->id)
            ->where('is_host', true)
            ->first();

        $questionNumber = $host?->current_question ?? 1;

        // Registrar jugador (si manejas tabla de jugadores)
        $player = ArenaPlayer::create([
            'name' => $request->player_name,
            'arena_game_id' => $arenaGame->id,
            'user_id' => Auth::check() ? Auth::id() : null,
            'question_number' => $questionNumber,
            'has_responded' => false,
        ]);
       // $quizId = $arenaGame->gameHistory->quiz_id;
        broadcast(new PlayerJoined($player, $arenaGame->id));


        // Guardar datos en la sesión para el jugador
        session([
            'player_name' => $player->name,
            'arena_player_id' => $player->id,
            'arena_game_pin' => $arenaGame->pin,
            'arena_game_id' => $arenaGame->id,
        ]);

        return view('quizzes.waiting', [
            'player' => $player,
            'arenaGameId' => $arenaGame->id,
            'pin' => $arenaGame->pin,
            'score'=>0,
        ]);
      //  return redirect()->route('arena.waiting');
    }

    public function removePlayer(Request $request, $arena)
    {
        //Log::info('Intentando eliminar jugador', [
//            'arena_game_id' => $arena,
//            'id' => $request->input('user_id')
//        ]);

        $userId = $request->input('user_id');

        if (!$userId) {
            return response()->json(['error' => 'user_id no enviado'], 400);
        }


        DB::table('arena_players')
            ->where('arena_game_id', $arena)
            ->where('user_id', $userId)
           // ->where('id', $userId)
            ->delete();

        session()->forget([
            'player_name',
            'arena_player_id',
            'arena_game_pin',
            'arena_game_id',
        ]);

      //  return redirect('quizzes.index');
        return response()->json(['status' => 'ok']);
    }

    public function showPlayerView($arenaGameId)
    {
        $arenaGame = ArenaGame::findOrFail($arenaGameId);  // Obtén el juego de la arena
        $quiz = $arenaGame->gameHistory->quiz;

        // Obtén el número de pregunta de la sesión o de la base de datos
        $player = ArenaPlayer::where('arena_game_id', $arenaGameId)->where('user_id', Auth::id())->first();

        // Obtén la pregunta actual
       // $question = $quiz->quizQuestions[$questionNumber - 1];

        $host = ArenaPlayer::where('arena_game_id', $arenaGameId)
            ->where('is_host', true)
            ->first();

        $questionId = $host?->current_question ?? 1;

   //     $questionId = $player->current_question;
        $question = QuizQuestion::find($questionId);
        if (!$question) {
            return redirect()->back()->with('error', 'Question not found.');
        }

       // foreach ($players as $player) {
        $player->last_answered_question_id = $player->current_question;
        $player->last_selected_answer_id = null;

            $player->has_responded = false;
            $player->current_question = $questionId;
            $player->question_started_at = now();
            $player->save();
        //  }

        return view('quizzes.player-answer',[
            'quiz' => $quiz,
            'arenaGameId' => $arenaGame->id,
            'question' => $question,
            'playersAnswered' => 0,
            'totalPlayers' => ArenaPlayer::where('arena_game_id', $arenaGameId)->count(),
            'questionNumber' => $questionId,
            'totalQuestions' => $quiz->num_questions,
            'playerScore' => $player->score,
            'timeLimit' => 20,  // Tiempo límite para responder
        ]);
    }


    public function playersAnsweredCount($arenaGameId)
    {
        $playersAnswered = ArenaPlayer::where('arena_game_id', $arenaGameId)
            ->where('is_host', false)
            ->where('has_responded', true)
            ->count();

        $totalPlayers = ArenaPlayer::where('arena_game_id', $arenaGameId)->where('is_host', false)->count();

        return response()->json([
            'answered' => $playersAnswered,
            'total' => $totalPlayers
        ]);
    }

    public function updatePlayerAnswer(Request $request, ArenaGame $arenaGame)
    {
       // Log::info('updatePlayerAnswer START', ['arena_game_id' => $arenaGame->id, 'user_id' => Auth::id()]);

        $player = ArenaPlayer::where('arena_game_id', $arenaGame->id)

            ->where('user_id', Auth::id())
            ->firstOrFail();

       // Log::info('Player found', ['player_id' => $player->id]);

        // Validar que el jugador esté en la partida y haya seleccionado una respuesta
        $request->validate([
            'selected_answer_id' => 'nullable|exists:quiz_answers,id',  // No requerido
            'question_id' => 'required|exists:quiz_questions,id',
        ]);

     //   Log::info('Request validated', $request->all());


        // Obtener la respuesta seleccionada
        $selectedAnswer = $request->selected_answer_id ? QuizAnswer::find($request->selected_answer_id) : null;
        $question = QuizQuestion::find($request->question_id);
//        Log::info('Selected answer and question retrieved', [
//            'selected_answer_id' => $selectedAnswer?->id,
//            'question_id' => $question->id,
//        ]);

        $score = $selectedAnswer ? $selectedAnswer->is_correct ? 100 : 0 : 0;

        // Actualizar el jugador con la respuesta seleccionada
        // Actualizar la respuesta del jugador
        $player->last_selected_answer_id = $selectedAnswer ? $selectedAnswer->id : null;
        $player->last_answered_question_id = $question->id;
        $player->score += $score; // Asignar puntos según la respuesta
        $player->has_responded = true; // El jugador ha respondido
        $player->save();

//        Log::info('Player updated', [
//            'player_id' => $player->id,
//            'score_added' => $score,
//            'total_score' => $player->score,
//        ]);

        // Redirigir al método showQuestionResult para procesar los resultados de la pregunta
        return redirect()->route('arena.show_question_result', [
            'arenaGame' => $arenaGame,
            'question' => $question,
            'score' => $score
        ])->with('message', 'Respuesta registrada.');
    }

    // Para uso interno en el controlador
    public function isQuestionReady(ArenaGame $arenaGame, QuizQuestion $question): bool
    {
        $arenaGame->load('players');
        $players = $arenaGame->players ->where('is_host', false);

        $respondedCount = $players ->where('has_responded', true)->count();
        $totalCount = $players->count();
        $allResponded = $respondedCount === $totalCount;
        $questionDuration = 21;


      //  $questionStart = $players->first()?->question_started_at;
       // $timeExpired = $questionStart && now()->diffInSeconds($questionStart) >= $questionDuration;

        $timeExpired = $players->first()?->question_started_at
            ? now()->diffInSeconds($players->first()->question_started_at) >= $questionDuration
            : false;
//        Log::info('Check status - internal', [
//            'allResponded' => $allResponded,
//            'timeExpired' => $timeExpired,
//        ]);

        return $allResponded || $timeExpired;
    }


    public function checkResultStatus(ArenaGame $arenaGame, QuizQuestion $question)
    {
        if (!ArenaGame::where('id', $arenaGame->id)->exists()) {
            return response()->json(['status' => 'finished']);
        }
        $arenaGame->load('players');
        $players = $arenaGame->players;
        $hostPlayer = $players->where('is_host', true)->first();

        // 🎯 Si el juego está finalizado
        if ($arenaGame->status === 'finished' || $hostPlayer?->current_question === null) {
            return response()->json(['status' => 'finished']);
        }

        // 🎯 Detectar si el host cambió de pregunta (comparar con jugadores normales)
        $nonHostPlayers = $players->where('is_host', false);
        $hostCurrentQuestion = $hostPlayer->current_question;

        $questionChanged = false;

        foreach ($nonHostPlayers as $player) {
            if ($player->current_question !== $hostCurrentQuestion) {
                // Actualizar el jugador
//                $player->last_answered_question_id = $player->current_question;
//                $player->current_question = $hostCurrentQuestion;
//                $player->has_responded = false;
//                $player->last_selected_answer_id = null;
//                $player->question_started_at = now();
//                $player->save();

                $questionChanged = true;
            }
        }

        if ($questionChanged) {
            return response()->json(['status' => 'next_question']);
        }

        // 🎯 Evaluar si mostrar resultados
        $respondedCount = $nonHostPlayers->where('has_responded', true)->count();
        $totalCount = $nonHostPlayers->count();
        $allResponded = $respondedCount === $totalCount;
        $questionDuration = 21;

        $timeExpired = $nonHostPlayers->first()?->question_started_at
            ? now()->diffInSeconds($nonHostPlayers->first()->question_started_at) >= $questionDuration
            : false;

        if ($allResponded || $timeExpired) {
            return response()->json(['status' => 'show_result']);
        }

        return response()->json(['status' => 'waiting']);
    }




    public function showQuestionResult(ArenaGame $arenaGame, QuizQuestion $question, $score)
    {
//        Log::info('showQuestionResult START', [
//            'arena_game_id' => $arenaGame->id,
//            'question_id' => $question->id,
//            'incoming_score' => $score
//        ]);
        $gameHistory = $arenaGame->gameHistory;
        $quiz = $gameHistory?->quiz;

        // Obtener todos los jugadores en esta partida
        $players = $arenaGame->players ->where('is_host', false);
      //  Log::info('Total players loaded', ['count' => $players->count()]);


        if (!$this->isQuestionReady($arenaGame, $question)) {
           // Log::info('Redirecting back to play because not all players responded and time not expired');

            return view('quizzes.player-results',['arenaGame' => $arenaGame,
                'waiting'=>true,
                'question' => $question,
                'score' => $score,
                'quiz' => $quiz,]);
        }

        // Obtener todas las respuestas posibles a la pregunta
        $questionAnswers = $question->quizQuestionAnswers()->get();

        // Identificar la respuesta correcta
        $correctAnswer = $questionAnswers->firstWhere('is_correct', true);
     //   Log::info('Correct answer determined', ['correct_answer_id' => $correctAnswer?->id]);

        // Calcular puntaje por jugador
        $playersWithAnswers = $players->map(function ($player) use ($question, $correctAnswer,$score) {
            $questionScore = 0;

            // Solo cuenta si el jugador respondió esta pregunta
            if ($player->last_answered_question_id === $question->id) {
                if ($player->last_selected_answer_id === $correctAnswer?->id) {
                    $questionScore = 100;
                }
            }


            // Actualizar puntaje
          //  $player->score += $score;//$questionScore;
           // $player->has_responded = false; // Reset para próxima pregunta
            $player->save();
//            Log::info('Player score updated in showQuestionResult', [
//                'player_id' => $player->id,
//                'question_score' => $score,
//                'total_score' => $player->score,
//            ]);

            return [
                'name' => $player->name,
                'player_id' => $player->id,
                'total_score' => $player->score,
                'question_score' => $score,
                'selected_answer_id' => $player->last_selected_answer_id,
            ];
        });

        // Calcular cuántos jugadores seleccionaron cada respuesta
        $answerStats = $questionAnswers->map(function ($answer) use ($arenaGame, $question) {
            $count = DB::table('arena_players')
                ->where('arena_game_id', $arenaGame->id)
                ->where('last_answered_question_id', $question->id)
                ->where('last_selected_answer_id', $answer->id)
                ->count();
//            Log::info('Answer stat counted', [
//                'answer_id' => $answer->id,
//                'count' => $count,
//            ]);

            return [
                'answer_id' => $answer->id,
                'answer_text' => $answer->answer_text,
                'count' => $count,
                'is_correct' => $answer->is_correct,
            ];
        });
        $responses = $arenaGame->players()
            ->where('is_host', false)
            ->where('last_answered_question_id', $question->id)
            ->with('lastSelectedAnswer')
            ->get();

        // Contar cuántos escogieron cada respuesta
        $answerCounts = $responses->groupBy('last_selected_answer_id')->map->count();
        $answerCounts = $answerCounts ?? collect(); // Garantiza que no sea null

        // Obtener respuesta correcta
        $correctAnswer = $question->quizQuestionAnswers()->where('is_correct', true)->first();

        // Ranking global
        $ranking = $arenaGame->players()
            ->where('is_host', false)
            ->orderByDesc('score')
            ->get();
       // $ranking = $playersWithAnswers->sortByDesc('total_score')->values();
     //   Log::info('Ranking generated', ['ranking' => $ranking->toArray()]);


      //  Log::info('Returning view with data');

        return view('quizzes.player-results', [
            'arenaGame' => $arenaGame,
            'quiz' => $quiz,
            'waiting'=>false,
            'question' => $question,
            'correctAnswer' => $correctAnswer,
            'answerStats' => $answerStats,
            'playersWithScores' => $playersWithAnswers,
            'ranking' => $ranking,
            'score' => $score,
            'responses' => $responses,
            'answerCounts' => $answerCounts,
        ]);
    }
    public function getCurrentQuestion(ArenaPlayer $player)
    {
        return response()->json([
            'current_question' => $player->current_question,
            'has_responded' => $player->has_responded,
        ]);
    }


}
