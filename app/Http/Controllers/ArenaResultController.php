<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreArenaResultRequest;
use App\Http\Requests\UpdateArenaResultRequest;
use App\Models\ArenaResult;

class ArenaResultController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $results = ArenaResult::with(['arenaGame', 'user'])->latest()->get();
        return view('arena_results.index', compact('results'));
    }

    public function create()
    {
        return view('arena_results.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArenaResultRequest $request)
    {
        $result = ArenaResult::create($request->validated());
        return redirect()->route('arena-results.index')->with('success', 'Resultado guardado correctamente.');

//        $result = ArenaResult::create($request->validated());
//        return response()->json($result, Response::HTTP_CREATED);

    }

    /**
     * Display the specified resource.
     */
    public function show(ArenaResult $arenaResult)
    {
        return view('arena_results.show', compact('arenaResult'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArenaResultRequest $request, ArenaResult $arenaResult)
    {
        $result = ArenaResult::update($request->validated());
        return redirect()->route('arena-results.index')->with('success', 'Resultado actualizado.');


//        $arenaResult->update($request->validated());
//        return response()->json($arenaResult);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArenaResult $arenaResult)
    {
        $arenaResult->delete();
        return redirect()->route('arena-results.index')->with('success', 'Resultado eliminado.');

    }
}
