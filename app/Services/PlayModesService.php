<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\Log;

class PlayModesService
{
    public static function verifyOpenQuestionWithAI($question,$userAnswer, $correctAnswer)
    {
      //  Log::info("ai supposed to check answer ");
        $apiKey = config('services.gemini.key');
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent?key=$apiKey";

        $prompt = "You are an expert evaluator. Given the question: '$question' and the user's answer: '$userAnswer', determine if the user's answer is factually and semantically correct based on your own knowledge of the topic. Only respond with: correct or incorrect. Do not include any explanation or additional text.";

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

     //   Log::info("AI verification response: '$text'");

        // Verificar explícitamente si la IA responde "correct" o "incorrect"
        if ($text === 'correct') {
           // Log::info("AI response is correct.");
            return true; // Respuesta correcta
        } elseif ($text === 'incorrect') {
           // Log::info("AI response is incorrect.");
            return false; // Respuesta incorrecta
        } else {
            Log::error("Unexpected AI verification response: $text");
            return false; // Considerar incorrecto si la respuesta no es ni "correct" ni "incorrect"
        }
    }

    public static function evaluateOpenQuestionWithAI($question, $userAnswer, $correctAnswer)
    {
      //  Log::info("AI evaluating open question with feedback");
        $apiKey = config('services.gemini.key');
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-pro:generateContent?key=$apiKey";

        $prompt = "You are an expert evaluator. Given the question: '$question' and the user's answer: '$userAnswer', decide whether the answer is factually and semantically correct based on your understanding of the question alone. "
            . "Respond strictly in JSON format like this: {\"result\": \"correct\" or \"incorrect\", \"feedback\": \"[brief feedback in English, max 150 characters]\"}. "
            . "Do not include any explanation or additional text. Only return the JSON object.";


        $postData = [
            "contents" => [
                [
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            Log::error('Curl error during evaluateWithAI: ' . curl_error($ch));
            curl_close($ch);
            return ['correct' => false, 'feedback' => 'There was an error processing your answer.'];
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Unexpected Gemini API response: ' . $response);
            return ['correct' => false, 'feedback' => 'There was an error processing your answer.'];
        }

        $jsonText = trim($data['candidates'][0]['content']['parts'][0]['text']);
        $jsonText = preg_replace('/^```json|```$/i', '', $jsonText); // elimina marcas ```json y ```
        $json = json_decode(trim($jsonText), true);

        if (!isset($json['result']) || !isset($json['feedback'])) {
            Log::error("Invalid JSON from Gemini: " . $data['candidates'][0]['content']['parts'][0]['text']);
            return ['correct' => false, 'feedback' => 'Invalid response from AI.'];
        }

        return [
            'correct' => strtolower($json['result']) === 'correct',
            'feedback' => substr($json['feedback'], 0, 150) // asegurarse del límite
        ];
    }


}
