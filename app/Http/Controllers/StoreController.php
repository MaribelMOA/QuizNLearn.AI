<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\XpPrice;
use App\Services\UsageService;

class StoreController extends Controller
{
    public function index()
    {
        $features = Feature::with('featureType')->get();
        $xpPrices = XpPrice::all();

        $userId = $user->id;
        $uses =UsageService::calculateAvailableUses($userId);
        // Estos valores vendrían de la suscripción del usuario o configuración
        $availableCreations = $uses['quiz_creation']['remaining']?? 4;
        $studyModeUses = $uses['study_mode']['remaining']?? 5;
        $arenaModeUses = $uses['arena_mode']['remaining']?? 6;
        $availableSummaryCreations = $uses['summary']['remaining']?? 4;


        return view('xp-store', compact('features', 'xpPrices'));
    }

}
