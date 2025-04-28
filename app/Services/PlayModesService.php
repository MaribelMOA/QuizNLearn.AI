<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\Log;

class PlayModesService
{
    public static function verifyOpenQuestionWithAI($question,$userAnswer, $correctAnswer)
    {
        Log::info("ai supposed to check answer ");
        $apiKey = config('services.gemini.key');
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent?key=$apiKey";

        $prompt = "Given the question: '$question', determine if the user answer: '$userAnswer' is semantically similar or correct compared to the correct answer: '$correctAnswer'. Only respond with 'correct' or 'incorrect'.";

        $postData = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => $prompt
                        ]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Curl error during verifyWithAI: ' . curl_error($ch));
            curl_close($ch);
            return false; // Considerar incorrecto si hay error
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Unexpected Gemini API response during verifyWithAI: ' . $response);
            return false; // Considerar incorrecto si respuesta inesperada
        }


        $text = strtolower(trim($data['candidates'][0]['content']['parts'][0]['text']));

        Log::info("AI verification response: '$text'");

        // Verificar explícitamente si la IA responde "correct" o "incorrect"
        if ($text === 'correct') {
            Log::info("AI response is correct.");
            return true; // Respuesta correcta
        } elseif ($text === 'incorrect') {
            Log::info("AI response is incorrect.");
            return false; // Respuesta incorrecta
        } else {
            Log::error("Unexpected AI verification response: $text");
            return false; // Considerar incorrecto si la respuesta no es ni "correct" ni "incorrect"
        }
    }

}
