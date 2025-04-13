<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeatureTypeRequest;
use App\Http\Requests\UpdateFeatureTypeRequest;
use App\Models\FeatureType;

class FeatureTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FeatureType::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeatureTypeRequest $request)
    {
        return FeatureType::create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(FeatureType $featureType)
    {
        return $featureType;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeatureTypeRequest $request, FeatureType $featureType)
    {
        $featureType->update($request->validated());
        return $featureType;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FeatureType $featureType)
    {
        $featureType->delete();
        return response()->noContent();
    }
}
