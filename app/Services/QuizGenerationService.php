<?php

namespace App\Services;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\DB;
class QuizGenerationService
{
    public static function generateQuestions1(Request $request, Quiz $quiz)
    {
        $request->validate(['content' => 'required|string']);

        $prompt = [
            [
                'role' => 'user',
                'content' => "Based on the following content, generate {$quiz->num_questions} quiz questions in JSON format, using only the following types: 1) Multiple Choice, 2) True or False, 3) Open Questions. For each question, include: type, text, options (if applicable), correct answer, and explanation.\n\nContent:\n" . $request->input('content')
            ]
        ];

        // Si aún no has configurado OpenAI, marca el punto de integración aquí:
        // $response = Http::withToken(env('OPENAI_API_KEY'))->post('https://api.openai.com/v1/chat/completions', [...])

        // Aquí iría un ejemplo simulado (hasta que conectes OpenAI):
        $exampleJson = '[
            {
                "type": "Multiple Choice",
                "text": "What is the capital of France?",
                "options": ["Berlin", "Madrid", "Paris", "Rome"],
                "correct_answer": "Paris",
                "explanation": "Paris is the capital city of France."
            },
            {
                "type": "True or False",
                "text": "The Earth is flat.",
                "correct_answer": false,
                "explanation": "Scientific evidence shows the Earth is spherical."
            }
        ]';

        $questions = json_decode($exampleJson, true);

        foreach ($questions as $item) {
            $type = QuestionType::where('name', $item['type'])->first();

            if (!$type) continue;

            $question = QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question_type_id' => $type->id,
                'question_text' => $item['text'],
            ]);

            if ($item['type'] === 'Multiple Choice') {
                foreach ($item['options'] as $option) {
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer_text' => $option,
                        'is_correct' => $option === $item['correct_answer'],
                        'explanation' => $option === $item['correct_answer'] ? $item['explanation'] : null,
                    ]);
                }
            } elseif ($item['type'] === 'True or False') {
                foreach ([true, false] as $value) {
                    QuizAnswer::create([
                        'question_id' => $question->id,
                        'answer_text' => $value ? 'True' : 'False',
                        'is_correct' => $value == $item['correct_answer'],
                        'explanation' => ($value == $item['correct_answer']) ? $item['explanation'] : null,
                    ]);
                }
            } elseif ($item['type'] === 'Open Questions') {
                QuizAnswer::create([
                    'question_id' => $question->id,
                    'answer_text' => '[Open-ended]',
                    'is_correct' => false,
                    'explanation' => $item['explanation'],
                ]);
            }
        }

        return redirect()->route('quizzes.index')->with('success', 'Quiz created with AI questions.');
    }

    public static function extractTextFromPDF2($file)
    {
        $path = $file->storeAs('temp_pdfs', Str::uuid() . '.pdf');
        $fullPath = storage_path('app/' . $path);
        $text = shell_exec("pdftotext '$fullPath' -");
        Storage::delete($path);
        return $text ?? '';
    }
    public static function extractTextFromPDF($file)
    {
        // Guardar el PDF en el almacenamiento temporal
        $path = $file->storeAs('temp_pdfs', Str::uuid() . '.pdf');
        $fullPath = storage_path('app/' . $path);

        // Convertir el PDF a imagen (usando pdftoppm, que también está en poppler-utils)
        $imagePath = storage_path('app/temp_images/' . Str::uuid() . '.png');
        shell_exec("pdftoppm " . escapeshellarg($fullPath) . " " . escapeshellarg($imagePath) . " -png");

        // Ejecutar OCR con Tesseract
        $text = shell_exec("tesseract " . escapeshellarg($imagePath) . " -");

        // Eliminar los archivos temporales
        Storage::delete($path);
        Storage::delete(str_replace('app/', 'app/temp_images/', $imagePath));

        return $text ?? '';
    }


    public static function fetchTextFromURL($url)
    {
        $html = @file_get_contents($url);
        if (!$html) return '';

        // Eliminar scripts y estilos
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);

        // Eliminar navegación común (simplemente por nombre de etiquetas o clases)
        $html = preg_replace('/<(nav|header|footer|aside)[^>]*>.*?<\/\1>/is', '', $html);

        // Eliminar comentarios HTML
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Eliminar etiquetas HTML restantes
        $text = strip_tags($html);

        // Normalizar espacios
        $text = preg_replace('/[\r\n\t ]+/', ' ', $text);
        $text = preg_replace('/ +/', ' ', $text);

        return trim($text);
//        try {
//            $response = Http::timeout(10)->get($url);
//            return strip_tags($response->body());
//        } catch (\Exception $e) {
//            return '';
//        }
    }

    public static function summarizeContent(string $content): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.huggingface.key'),
        ])->post('https://api-inference.huggingface.co/models/facebook/bart-large-cnn', [
            'inputs' => $content,
        ]);

        if ($response->successful() && isset($response[0]['summary_text'])) {
            return $response[0]['summary_text'];
        }

        return $content;
    }


    public static function generateQuestions(string $content, Quiz $quiz)
    {

    }
    public static function mapQuestionTypeLabel(string $type): string
    {
        return match ($type) {
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True or False',
            'open_ended' => 'Open Question',
            default => ucfirst(str_replace('_', ' ', $type)), // fallback decente
        };
    }


    protected static function getQuestionTypeDistribution(Quiz $quiz,array $questionTypes)
    {
        $types = $quiz->questionTypes()->pluck('name'); // ['Multiple Choice', 'True or False', ...]
        $total = $quiz->num_questions;
        $count = count($questionTypes);

        if ($count === 0) {
            throw new \InvalidArgumentException('No question types selected for distribution.');
        }

        $base = intdiv($total, $count);
        $leftover = $total % $count;

        $distribution = [];
        foreach ($types as $i => $type) {
            $distribution[$type] = $base + ($i < $leftover ? 1 : 0);
        }

        return $distribution; // ['Multiple Choice' => 3, 'True or False' => 2, ...]
    }

    protected static function buildPrompt(string $content, Quiz $quiz,array $questionTypes): string
    {
        $distribution = self::getQuestionTypeDistribution($quiz,$questionTypes);

        $prompt = "You are an expert exam generator. Based on the following content:\n\n---\n$content\n---\n\n";
        $prompt .= "Create exactly {$quiz->num_questions} questions of {$quiz->difficulty_level} level.\n";
        $prompt .= "Distribute them as follows:\n";

        foreach ($distribution as $type => $count) {
            $label = self::mapQuestionTypeLabel($type);
            $prompt .= "- $count questions of type \"$label\"\n";
        }

        $prompt .= <<<EOT

For each question, respond in JSON format with this structure:

{
  "question_text": "The question text",
  "question_type": "Multiple Choice | True or False | Open Questions",
  "answers": [
    {
      "answer_text": "The answer text",
      "is_correct": true | false,
      "explanation": "Explanation of why it is correct or incorrect"
    }
  ]
}

Generate the entire JSON as an array. DO NOT explain anything outside the JSON.

EOT;

        return $prompt;
    }


    public static function generateWithOpenAI(string $content, Quiz $quiz, array $questionTypes)
    {
        $prompt = self::buildPrompt($content, $quiz,$questionTypes);

        $client = new \GuzzleHttp\Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openai_key'),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Responde solo con JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
            ],
        ]);

        $result = json_decode($response->getBody(), true);
        $json = $result['choices'][0]['message']['content'] ?? '[]';

        return json_decode($json, true);
    }

    public static function generateWithGemini(string $content, Quiz $quiz,  array $questionTypes)
    {
        $prompt = self::buildPrompt($content, $quiz, $questionTypes);
        $apiKey = config('services.gemini_key');

        $client = new \GuzzleHttp\Client();
        $response = $client->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=$apiKey", [
            'json' => [
                'contents' => [[
                    'parts' => [[ 'text' => $prompt ]]
                ]]
            ]
        ]);

        $result = json_decode($response->getBody(), true);
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? '[]';

        return json_decode($text, true);
    }

    public static function storeGeneratedQuestions(array $questions, Quiz $quiz): void
    {
        DB::transaction(function () use ($questions, $quiz) {
            foreach ($questions as $item) {
                // Buscar el tipo
                $type = QuestionType::where('name', $item['question_type'])->firstOrFail();

                // Guardar la relación quiz - tipo si no existe
                $quiz->questionTypes()->syncWithoutDetaching([$type->id]);

                // Crear la pregunta
                $question = QuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question_type_id' => $type->id,
                    'question_text' => $item['question_text'],
                ]);

                // Si hay respuestas (solo en Multiple Choice o True/False)
                if (!empty($item['answers']) && is_array($item['answers'])) {
                    foreach ($item['answers'] as $answerData) {
                        QuizAnswer::create([
                            'question_id' => $question->id,
                            'answer_text' => $answerData['answer_text'],
                            'is_correct' => $answerData['is_correct'],
                            'explanation' => $answerData['explanation'] ?? null,
                        ]);
                    }
                }
            }
        });
    }







}
