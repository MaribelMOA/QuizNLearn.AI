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

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\DB;
class QuizGenerationService
{
    public static function generateQuestions1(Request $request, Quiz $quiz)
    {
        $request->validate(['content' => 'required|string']);

        $prompt = [
            [
                'role' => 'user',
                'content' => "Based on the following content, generate {$quiz->num_questions} quiz questions in JSON format, using only the following types: 1) Multiple Choice, 2) True or False, 3) Open Question. For each question, include: type, text, options (if applicable), correct answer, and explanation.\n\nContent:\n" . $request->input('content')
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
            } elseif ($item['type'] === 'Open Question') {
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

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return '';
        }
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

//    public static function summarizeContent(string $content): string
//    {
//        $response = Http::withHeaders([
//            'Authorization' => 'Bearer ' . config('services.huggingface.key'),
//        ])->post('https://api-inference.huggingface.co/models/facebook/bart-large-cnn', [
//            'inputs' => $content,
//        ]);
//
//        if ($response->successful() && isset($response[0]['summary_text'])) {
//            return $response[0]['summary_text'];
//        }
//
//        return $content;
//    }

// Función para limpiar caracteres ilegales
    public static function cleanContent($text)
    {
        // Forzar UTF-8 correcto
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Reemplazar caracteres ilegales (�) por un espacio
        $text = str_replace('�', ' ', $text);

        // También eliminar cualquier cosa fuera del rango UTF-8 básico
        $text = preg_replace('/[^\x09\x0A\x0D\x20-\x7E\xA0-\xFF]/u', '', $text);

        // Opcional: limpiar múltiples espacios
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }


    public static function summarizeContent(string $text): string
    {
        $apiKey = config('services.huggingface.key'); // Pon tu API KEY en tu .env
        $endpoint = 'https://api-inference.huggingface.co/models/facebook/bart-large-cnn'; // Modelo de resumen

        $response = Http::withHeaders([
            'Authorization' => "Bearer $apiKey",
        ])->post($endpoint, [
            'inputs' => $text,
        ]);

        if ($response->successful()) {
            $result = $response->json();
            return $result[0]['summary_text'] ?? $text; // Regresar el resumen
        }

        // Si falla, devolver el texto original
        Log::error('Failed to summarize content: ' . $response->body());
        return $text;
    }
    public static function  summarizeWithGemini(string $text): ?string
    {
        $apiKey = config('services.gemini.key');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

        $postData = [
            "contents" => [
                [
                    "parts" => [
                        [
                            "text" => "Summarize the following text preserving all main ideas for quiz generation.Limit the summary to a maximum of 5000 characters.\n\n$text"
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
            Log::error('Curl error: ' . curl_error($ch));
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Unexpected API response: ' . $response);
            return null;
        }

        return $data['candidates'][0]['content']['parts'][0]['text'];
    }




    public static function mapQuestionTypeLabel(string $type): string
    {
        return match ($type) {
            'multiple_choice' => 'Multiple Choice',
            'true_or_false' => 'True or False',
            'open_question' => 'Open Question',
            default => ucfirst(str_replace('_', ' ', $type)), // fallback decente
        };
    }
    public static function mapLabelToQuestionType(string $label): string
    {
        return match (strtolower($label)) {
            'Multiple Choice' => 'multiple_choice',
            'true_or_false' => 'true_false',
            'Open Question' => 'open_question',
            default => strtolower(str_replace(' ', '_', $label)), // fallback para otros casos
        };
    }



    public static function getQuestionTypeDistribution(Quiz $quiz, array $questionTypes)
    {
        // Mapear las etiquetas de pregunta seleccionadas
        $mappedTypes = array_map(function ($type) {
            return self::mapLabelToQuestionType($type);
        }, $questionTypes);

        Log::info('Selected question types after mapping: ' . json_encode($mappedTypes));

        $total = $quiz->num_questions;
        $count = count($mappedTypes);

        if ($count === 0) {
            throw new \InvalidArgumentException('No question types selected for distribution.');
        }

        $base = intdiv($total, $count);
        $leftover = $total % $count;

        // Distribución final con la clave como tipo legible
        $distribution = [];
        foreach ($mappedTypes as $i => $type) {
            $distribution[self::mapQuestionTypeLabel($type)] = $base + ($i < $leftover ? 1 : 0);
        }
        Log::info('Total number of questions: ' . $quiz->num_questions);
        Log::info('Distribution calculation: base questions per type: ' . $base);
        Log::info('Leftover questions: ' . $leftover);
        Log::info('Final question distribution: ' . json_encode($distribution));

        return $distribution; // Ejemplo: ['Multiple Choice' => 3, 'True or False' => 2]
    }


    public static function buildPrompt(string $content, Quiz $quiz,array $questionTypes): string
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
  "question_text": "The question text(Maximum 200 characters)",
  "question_type": "Multiple Choice | True or False | Open Question",
  "answers": [
    {
      "answer_text": "The answer text",
      "is_correct": true | false,
      "explanation": "Explanation of why it is correct or incorrect(maximum 150 characters)"
    }
  ]
}
Special instructions for "True or False" questions:
- ALWAYS include exactly two answers: one with "True" and one with "False".
- Specify clearly which one is correct (is_correct: true) and which one is incorrect (is_correct: false).
- Provide an explanation for both answers (each explanation must be concise and up to 150 characters).

Generate the entire JSON as an array. Please make sure the explanations are concise and no longer than 150 characters.
DO NOT explain anything outside the JSON.



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


        $apiKey = config('services.gemini.key');
        Log::info('Gemini API Key: ' . $apiKey);

        Log::info('GEMINI_API_KEY: ' . env('GEMINI_API_KEY'));


        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey";

        $prompt = self::buildPrompt($content, $quiz, $questionTypes);

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
            Log::error('Curl error during quiz generation: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        $data = json_decode($response, true);

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            Log::error('Unexpected Gemini API response during quiz generation: ' . $response);
            return null;
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'];
        // ✅ Limpiar las comillas triples y la palabra "json"
        $text = preg_replace('/^```json|```$/m', '', trim($text));
        $text = trim($text); // Quitar espacios en blanco sobrantes


        // Guardar directamente el texto en el campo quiz_data
        $quiz->quiz_data = $text;
        $quiz->save();

        Log::info("Generated quiz: $text");

        return self::saveQuiz($text, $quiz, $questionTypes);
    }
    public static function saveQuiz(string $text, $quiz, $questionTypes)
    {
        // Decodificar las preguntas
        $questions = self::decodeQuestions($text);
        if (!$questions) {
            return null; // Si las preguntas no se generaron correctamente, salir
        }

        $questionsSaved = self::saveQuestions($quiz, $questions, $questionTypes);
        $answersSaved = self::saveAnswers($quiz, $questions);

        if (!$questionsSaved || !$answersSaved) {
            return false;
        }

        Log::info("Quiz generated and stored successfully.");
        return true;
    }

    public static function decodeQuestions($text)
    {
        // Decodificar el JSON y preparar las preguntas para guardarlas
        $questions = json_decode($text, true);

        if (empty($questions)) {
            Log::error('No questions generated or invalid format.');
            return null;
        }

        return $questions;
    }

    public static function saveQuestions($quiz, $questions, $questionTypes)
    {
        $allSaved = true;

        foreach ($questions as $questionData) {
            Log::info("Processing question: " . $questionData['question_text']);

            $questionTypeLabel = $questionData['question_type'];
            $mappedType = QuizGenerationService::mapLabelToQuestionType($questionTypeLabel);
            Log::info("Mapped question type: " . $mappedType);

            $type = QuestionType::where('name', $mappedType)->first();

            if (!$type) {
                Log::error("Question type not found: " . $mappedType);
                $allSaved = false;
                continue;
            }

            $question = $quiz->quizQuestions()->create([
                'question_text' => $questionData['question_text'],
                'question_type_id' => $type->id
            ]);

            if (!$question) {
                Log::error("Failed to save question: " . $questionData['question_text']);
                $allSaved = false;
                continue;
            }

            $quiz->questionTypes()->syncWithoutDetaching([$type->id]);
           Log::info("Question saved: " . $question->question_text);
        }

        return $allSaved;
    }




    public static function saveAnswers($quiz, $questions)
    {
        $allSaved = true;

        foreach ($questions as $questionData) {
            $question = $quiz->quizQuestions()->where('question_text', $questionData['question_text'])->first();
            if (!$question) {
                Log::error("Question not found: " . $questionData['question_text']);
                $allSaved = false;
                continue;
            }

            foreach ($questionData['answers'] as $answerData) {
                $answer = $question->quizQuestionAnswers()->create([
                    'answer_text' => $answerData['answer_text'],
                    'is_correct' => $answerData['is_correct'],
                    'explanation' => $answerData['explanation'] ?? null,
                ]);

                if (!$answer) {
                    Log::error("Failed to save answer for question: " . $question->question_text);
                    $allSaved = false;
                }
            }
        }

        return $allSaved;
    }


    // Función para generar el contenido HTML del PDF
    public static function generatePdfContent($data)
    {
        // Obtener los datos del quiz
        $quiz = $data['quiz'];

        // Obtener la fecha y hora actual
        $date = now()->format('d/m/Y');

        // Iniciar el contenido HTML para el PDF
        $content = "
    <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .quiz-header { text-align: center; margin-bottom: 20px; }
                .question { margin-bottom: 20px; }
                .options { margin-left: 20px; }
                .info-line { margin-top: 10px; text-align: left; }
                .info-line p { display: inline-block; margin-right: 20px; }

            </style>
        </head>
        <body>
            <div class='quiz-header'>
                <h1>Quiz: {$quiz->title}</h1>
                <div class='info-line'>
                    <p><strong>Name:</strong> ___________________________________</p>
                    <p><strong>Date:</strong> {$date}</p>
                    <p><strong>Score:</strong> ____ /  {$quiz->num_questions}    </p>

                </div>
                <p>Difficulty Level: {$quiz->difficulty_level}</p>

                <p><strong>Instructions:</strong> Read each question carefully and answer accordingly.</p>
            </div>";

        // Generar preguntas y respuestas
        foreach ($data['questions'] as $index => $question) {
            $content .= "<div class='question'>
                        <p><strong>Question " . ($index + 1) . ":</strong> {$question->question_text}</p>";

            // Dependiendo del tipo de pregunta, mostrar las opciones
            if ($question->type->name == 'multiple_choice' || $question->type->name == 'true_or_false') {
                $content .= "<div class='options'>";
                foreach ($question->quizQuestionAnswers as $key => $answer) {
                    $content .= "<p>" . chr(65 + $key) . ". {$answer->answer_text}</p>";
                }
                $content .= "</div>";
            } elseif ($question->type->name == 'open_question') {
                $content .= "<p><em>Open-ended question</em></p>";
            }

            $content .= "</div>";
        }

        // Cerrar el contenido HTML
        $content .= "</body></html>";

        return $content;
    }





}
