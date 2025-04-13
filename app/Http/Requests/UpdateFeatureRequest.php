<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'feature_type_id' => 'sometimes|exists:feature_types,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'xp_price' => 'sometimes|integer|min:0',
        ];
    }
}
