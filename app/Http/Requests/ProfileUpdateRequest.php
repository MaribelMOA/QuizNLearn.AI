<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\models\Plan;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $plans = Plan::all()->keyBy('id');
        $selectedPlan = $plans[$this->plan_id] ?? null;
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'plan_id' => ['required', 'exists:plans,id'],
            'payment_method' => [
                Rule::requiredIf(function () use ($selectedPlan) {
                    return $selectedPlan && $selectedPlan->price > 0;
                }),
                'in:paypal,stripe',
            ],

        ];
    }
}
