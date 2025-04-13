<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGameHistoryRequest;
use App\Http\Requests\UpdateGameHistoryRequest;
use App\Models\GameHistory;
use Illuminate\Http\Response;

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
}
