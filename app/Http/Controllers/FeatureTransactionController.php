<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeatureTransactionRequest;
use App\Http\Requests\UpdateFeatureTransactionRequest;
use App\Models\FeatureTransaction;

class FeatureTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FeatureTransaction::with(['user', 'feature'])->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeatureTransactionRequest $request)
    {
        $transaction = FeatureTransaction::create($request->validated());
        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(FeatureTransaction $featureTransaction)
    {
        return $featureTransaction->load(['user', 'feature']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeatureTransactionRequest $request, FeatureTransaction $featureTransaction)
    {
        $featureTransaction->update($request->validated());
        return response()->json($featureTransaction);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeatureTransaction $featureTransaction)
    {
        $featureTransaction->delete();
        return response()->json(null, 204);
    }
}
