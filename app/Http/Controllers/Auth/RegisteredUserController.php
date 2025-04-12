<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\UserPlanController;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\models\UserPlan;
use Illuminate\Validation\Rule;


class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        $plans = Plan::all(); // Obtén todos los planes disponibles
        return view('auth.register', compact('plans'));
       // return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'plan_id' => ['required', 'exists:plans,id'],  // Validar plan
            'payment_method' => Rule::requiredIf(function () use ($request) {
                $plan = \App\Models\Plan::find($request->plan_id);
                return $plan && $plan->price > 0;
            }),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

        ]);

        $plan = Plan::findOrFail($request->plan_id);

        // Asignar customer_id y payment_method_id (si están presentes)
        $user->update([
            'customer_id' => 'CUSTOMER-' . uniqid(),  // Generación de un ID de cliente simulado.Regularme el API lod aria
            'payment_method_id' => $plan->price > 0 ? $request->payment_method : null,
            'current_plan_id' => $plan->id,
        ]);
        UserPlan::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'start_date' => now(),
            'end_date' => now()->addDays(30), // 30 días de duración
        ]);

        event(new Registered($user));

       // Auth::login($user);



        //return redirect(route('login', absolute: false));
        return redirect()->route('login')->with('registered', true);

    }
}
