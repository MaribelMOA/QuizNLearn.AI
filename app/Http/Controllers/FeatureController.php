<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeatureRequest;
use App\Http\Requests\UpdateFeatureRequest;
use App\Models\Feature;

class FeatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $features = Feature::all();
        return view('features.index', compact('features'));

        //return Feature::with('featureType')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeatureRequest $request)
    {
      //  return Feature::create($request->validated());
        $features = Feature::all();
        return view('features.index', compact('features'));

    }

    /**
     * Display the specified resource.
     */
    public function show(Feature $feature)
    {
        //Carga la relación featureType del modelo Feature y devuelve el modelo $feature con esa relación ya incluida.
        return $feature->load('featureType');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFeatureRequest $request, Feature $feature)
    {
        $feature->update($request->validated());
        return $feature;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Feature $feature)
    {
        $feature->delete();
        return response()->noContent();
    }



}
