<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateXpPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'xp_amount' => 'sometimes|integer|min:1', // XP debe ser un número entero mayor que 0
            'price' => 'sometimes|numeric|min:0', // El precio debe ser un número mayor o igual que 0
        ];
    }
}
