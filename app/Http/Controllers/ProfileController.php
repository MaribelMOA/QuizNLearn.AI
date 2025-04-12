<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\Plan;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
            'plans' => Plan::all(),
            "currentPlanId" => optional(auth()->user()->userPlan)->plan_id,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->has('plan_id')) {
            $userPlan = $request->user()->userPlan;

            if ($userPlan) {
                $userPlan->update([
                    'plan_id' => $request->plan_id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(), // o lo que aplique
                ]);
                if ($request->user()->plan && $request->user()->plan->price == 0) {
                    $request->user()->payment_method_id = null;
                }
            } else {
                $request->user()->userPlan()->create([
                    'plan_id' => $request->plan_id,
                    'start_date' => now(),
                    'end_date' => now()->addMonth(),
                ]);
                //ADD RESOURCES, FUNCTIONALITIES,ETC
            }
        }

        if ($request->has('payment_method')) {
            $request->user()->payment_method_id = $request->payment_method;
        }

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updatePlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'payment_method' => 'required|string|in:paypal,stripe',
        ]);

        $user = auth()->user();
        $user->update([
            'plan_id' => $request->plan_id,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->back()->with('status', 'Plan updated successfully!');
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
