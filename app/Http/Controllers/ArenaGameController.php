<?php

namespace App\Http\Controllers;

use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use App\Http\Requests\StoreArenaGameRequest;
use App\Http\Requests\UpdateArenaGameRequest;
use App\Models\ArenaGame;
use App\Models\ArenaPlayer;
use App\Models\GameHistory;
use App\Models\UserPlan;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ->orderByRaw('COALESCE(last_game_history, quizzes.created_at) DESC')
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
             $arenaModeUses = 50;
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
        $currentPlayersCount = ArenaPlayer::where('arena_game_id', $arenaGame->id)->count();

        // Obtener el límite de jugadores desde el plan del host
        $userPlan = $arenaGame->gameHistory->user->plan;
        $maxPlayers = $userPlan ? $userPlan->max_arena_players : 0;

        if ($maxPlayers > 0 && $currentPlayersCount >= $maxPlayers) {
            // El juego está lleno, no permitir que más jugadores se unan
            return redirect()->back()->withErrors(['pin' => 'The game is full. No more players can join.']);
        }

        // Registrar jugador (si manejas tabla de jugadores)
        $player = ArenaPlayer::create([
            'name' => $request->player_name,
            'arena_game_id' => $arenaGame->id,
            'user_id' => Auth::check() ? Auth::id() : null,
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
        ]);
    }




}
