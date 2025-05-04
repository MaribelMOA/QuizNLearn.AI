<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use App\Models\FeatureTransaction;
use App\Models\FeatureType;
use App\Models\XpPrice;
use App\Services\UsageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class StoreController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $features = Feature::with('featureType')->get();
        $xpPrices = XpPrice::all();
        $grouped = FeatureType::with('features')->get();

        $userId = $user->id;
        $uses =UsageService::calculateAvailableUses($userId);
        // Estos valores vendrían de la suscripción del usuario o configuración
        $availableCreations = $uses['quiz_creation']['remaining']?? 4;
        $studyModeUses = $uses['study_mode']['remaining']?? 5;
        $arenaModeUses = $uses['arena_mode']['remaining']?? 6;
        $availableSummaryCreations = $uses['summary_creation']['remaining']?? 4;

        return view('shop.index', compact(
            'features',
            'xpPrices',
            'grouped',
            'availableCreations',
            'studyModeUses',
            'arenaModeUses',
            'availableSummaryCreations'
        ));
       // return view('xp-store', compact('features', 'xpPrices'));
    }

    public function purchasePackage(Request $request)
    {
        $package = XpPrice::findOrFail($request->xp_price_id);
        $user = auth()->user();

        // Aquí debería integrarse con Stripe u otro método de pago.
        // Simulación de compra directa (sólo para pruebas):
        $user->xp += $package->xp_amount;
        $user->save();

        return back()->with('success', " You have successfully gained " . $package->xp_amount . "XP exitosamente!");
    }

    public function purchaseFeature(Request $request)
    {
        Log::info('Iniciando proceso de compra de feature.', ['user_id' => Auth::id()]);

        $request->validate([
            'feature_id' => 'required|exists:features,id',
        ]);

        $user = Auth::user();
        $featureId = $request->input('feature_id');
        Log::info('Feature ID recibido.', ['feature_id' => $featureId]);

        // Obtener la feature
        $feature = Feature::with('featureType')->findOrFail($featureId);
        Log::info('Feature encontrada.', ['feature' => $feature->toArray()]);

        // Calcular usos disponibles actuales
        $uses = UsageService::calculateAvailableUses($user->id);
        Log::info('Usos disponibles calculados.', ['uses' => $uses]);

        $featureCode = $feature->featureType->code;

        // Validación: ¿tiene sentido comprar más?
        if (!isset($uses[$featureCode])) {
            Log::warning('Intento de compra de feature no válida.', ['feature_code' => $featureCode]);
            return redirect()->back()->with('error', 'This feature cannot be purchased.');
        }


        // Verificar si el usuario tiene suficiente XP
        if ($user->xp < $feature->xp_price) {
            Log::info('XP insuficiente para la compra.', [
                'user_xp' => $user->xp,
                'xp_price' => $feature->xp_price
            ]);
            return redirect()->back()->with('error', 'You do not have enough XP points for this purchase.');
        }

        try {
            DB::transaction(function () use ($user, $feature) {
                Log::info('Iniciando transacción de compra de feature.');

                // Registrar la compra
                FeatureTransaction::create([
                    'user_id' => $user->id,
                    'feature_id' => $feature->id,
                    'quantity' => 1,
                    'price_xp' => $feature->xp_price,
                ]);
                Log::info('Transacción registrada.');

                // Restar XP al usuario
                $user->xp -= $feature->xp_price;
                $user->save();

                Log::info('XP descontado y usuario actualizado.', ['nuevo_xp' => $user->xp_points]);
            });

            Log::info('Compra completada exitosamente.');
            return redirect()->back()->with('success', 'Feature purchased successfully.');
        } catch (\Exception $e) {
            Log::error('Error durante la compra de feature.', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while processing the purchase.');
        }
    }


}
