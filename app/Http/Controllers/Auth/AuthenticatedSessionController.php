<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use App\Models\UserPlan; // Asegúrate de incluir tu modelo
use Carbon\Carbon;
use App\Http\Controllers\UserPlanController;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $userPlanController = new UserPlanController();
        $userPlanController->createOrUpdateUserPlan(Auth::id());


        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function checkAndUpdatePlan()
    {
        $user = Auth::user();

        $currentUserPlan = UserPlan::where('user_id', $user->id)
            ->where('end_date', '<', now()) // Verificamos si ha expirado
            ->first();

        if ($currentUserPlan) {
            // Si el plan ha expirado, actualizamos las fechas
            $currentUserPlan->update([
                'start_date' => now(),
                'end_date'   => now()->addDays(30), // Extiende el plan por 30 días
            ]);

            // Actualizar los recursos según el plan
            //$this->updateUserResources($user, $currentUserPlan->plan);
        }
    }
}
