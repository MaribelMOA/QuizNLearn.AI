<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArenaResultRequest extends FormRequest
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
            'arena_game_id' => 'required|exists:arena_games,id',
            'user_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:0',
            'rank' => 'required|integer|min:1',
        ];
    }
}
