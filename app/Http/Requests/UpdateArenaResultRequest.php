<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArenaResultRequest extends FormRequest
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
            'arena_game_id' => 'sometimes|exists:arena_games,id',
            'user_id' => 'sometimes|exists:users,id',
            'score' => 'sometimes|integer|min:0',
            'rank' => 'sometimes|integer|min:1',
        ];
    }
}
