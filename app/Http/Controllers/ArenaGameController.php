<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArenaGameRequest;
use App\Http\Requests\UpdateArenaGameRequest;
use App\Models\ArenaGame;
use App\Services\UsageService;
use Illuminate\Http\Response;

use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
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
}
