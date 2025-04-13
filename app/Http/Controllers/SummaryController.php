<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSummaryRequest;
use App\Http\Requests\UpdateSummaryRequest;
use App\Models\Summary;

use Illuminate\Support\Facades\Auth;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $summaries = Summary::where('user_id', Auth::id())->latest()->get();
        return view('summaries.index', compact('summaries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('summaries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSummaryRequest $request)
    {
        $summary = Summary::create([
            ...$request->validated(),
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('summaries.index')->with('success', 'Resumen creado con éxito.');

    }

    /**
     * Display the specified resource.
     */
    public function show(Summary $summary)
    {
        $this->authorize('view', $summary);
        return view('summaries.show', compact('summary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Summary $summary)
    {
        $this->authorize('update', $summary);
        return view('summaries.edit', compact('summary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSummaryRequest $request, Summary $summary)
    {
        $this->authorize('update', $summary);

        $summary->update($request->validated());

        return redirect()->route('summaries.index')->with('success', 'Resumen actualizado.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Summary $summary)
    {
        $this->authorize('delete', $summary);
        $summary->delete();

        return redirect()->route('summaries.index')->with('success', 'Resumen eliminado.');

    }
}
