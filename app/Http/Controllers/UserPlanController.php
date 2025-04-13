<?php

namespace App\Http\Controllers;

use App\Models\UserPlan;
use App\Models\Plan;
use App\Services\UsageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;



class UserPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userPlans = UserPlan::all();
        return response()->json($userPlans);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // return view('user_plans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Crear el nuevo plan de usuario
        $userPlan = UserPlan::create($validated);

        // Devolver una respuesta de éxito
        return response()->json($userPlan, 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(UserPlan $userPlan)
    {
        return response()->json($userPlan);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserPlan $userPlan)
    {
        // return view('user_plans.edit', compact('userPlan'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserPlan $userPlan)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        // Actualizar el plan de usuario
        $userPlan->update($validated);

        return response()->json($userPlan);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserPlan $userPlan)
    {
        $userPlan->delete();

        return response()->json(['message' => 'User Plan deleted successfully']);

    }

    public function createOrUpdateUserPlan($userId)
    {
        $currentUserPlan = UserPlan::where('user_id', $userId)
            ->latest('end_date')
            ->first();

        if (!$currentUserPlan) {
            // Crear un nuevo plan si no existe
            UserPlan::create([
                'user_id'    => $userId,
                'start_date' => now(),
                'end_date'   => now()->addDays(30),
            ]);
        } elseif ($currentUserPlan->end_date < now()) {
            // Actualizar el plan si ha expirado
            $currentUserPlan->update([
                'start_date' => now(),
                'end_date'   => now()->addDays(30),
            ]);
        }

        $usos =UsageService::calculateAvailableUses($userId);
      //  return response()->json($usos);

    }


    public function subscribe(Request $request)
    {
        // Acceder a los datos del request usando input() o get()
        $plan_id = $request->input('plan_id');  // Accediendo a plan_id
        $payment_method_id = $request->input('payment_method_id');  // Accediendo a payment_method_id

        // Validar si los datos están presentes
        if (!$plan_id || !$payment_method_id) {
            return back()->withErrors(['error' => 'Faltan los datos necesarios para la suscripción.']);
        }

        // Buscar el plan usando plan_id
        $plan = Plan::findOrFail($plan_id);

        // Si el plan cuesta más de 0, validar que se haya enviado método de pago
        if ($plan->price > 0 && empty($payment_method_id)) {
            return back()->withErrors(['payment_method_id' => 'Este plan requiere un método de pago.']);
        }

        // Simulación de validación de método de pago
        if ($plan->price > 0 && !str_starts_with($payment_method_id, 'SIMULATED-')) {
            return back()->withErrors(['payment_method_id' => 'Método de pago inválido (usa SIMULATED-XXXX).']);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Buscar si ya existe un UserPlan para este usuario
        $currentUserPlan = UserPlan::where('user_id', $user->id)->first();

        if ($currentUserPlan) {
            // Si ya existe un plan, se actualizan las fechas
            $currentUserPlan->update([
                'start_date' => now(),
                'end_date' => now()->addDays(30), // Se extiende el plan por 30 días más
            ]);
        } else {
            // Si no existe un plan, se crea un nuevo registro en UserPlan
            UserPlan::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'start_date' => now(),
                'end_date' => now()->addDays(30), // 30 días de duración
            ]);
        }

        // Actualizar el método de pago si es necesario
        $user->update([
            'payment_method_id' => $plan->price > 0 ? $payment_method_id : null,
        ]);

        // Si todo es correcto, puedes redirigir o devolver alguna respuesta
       // return redirect('/dashboard')->with('success', 'Te has suscrito correctamente al plan "' . $plan->name . '"');
    }



}
