<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreXpPriceRequest;
use App\Http\Requests\UpdateXpPriceRequest;
use App\Models\XpPrice;
use App\Models\User;
use Illuminate\Http\Request;

class XpPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return XpPrice::all();
//        $xpPrices = XpPrice::all();
//
//        // Pasar los datos a la vista
//        return view('xpPrices.index', compact('xpPrices'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreXpPriceRequest $request)
    {
        // Crear un nuevo precio de XP con los datos validados
        $xpPrice = XpPrice::create($request->validated());

        // Retorna la respuesta con el objeto creado
        return response()->json($xpPrice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(XpPrice $xpPrice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateXpPriceRequest $request, XpPrice $xpPrice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(XpPrice $xpPrice)
    {
        //
    }


    /**
     * Sumar XP al usuario basado en una característica.
     */
    public function addXpToUser(Request $request, $userId)
    {
        // Validar que se pase un 'feature_name' en el request
        $request->validate([
            'feature_name' => 'required|string|exists:xp_prices,feature_name',
        ]);

        // Obtener al usuario
        $user = User::findOrFail($userId);

        // Obtener el precio de XP según la funcionalidad seleccionada
        $xpPrice = XpPrice::where('feature_name', $request->feature_name)->first();

        if (!$xpPrice) {
            return response()->json([
                'message' => 'No se encontró el precio para esta funcionalidad.',
            ], 404);
        }

        // Obtener la cantidad de XP requerida
        $xpRequired = $xpPrice->xp_amount;

        // Validar si el usuario tiene suficiente XP para la acción
        if ($user->xp >= $xpRequired) {
            // Restar los XP del usuario
            $user->xp -= $xpRequired;
            $user->save();

            // Realizar la acción (como registrar el uso, si es necesario)
            // Ejemplo: History::create(['user_id' => $user->id, 'action' => $request->feature_name, 'xp_used' => $xpRequired]);

            return response()->json([
                'message' => 'XP agregados correctamente al usuario.',
                'xp_balance' => $user->xp
            ]);
        } else {
            return response()->json([
                'message' => 'No tienes suficiente XP para realizar esta acción.',
            ], 400);
        }
    }
}
