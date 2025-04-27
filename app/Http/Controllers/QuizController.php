<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use App\models\GameHistory;
use App\Models\QuizCreation;
use App\Models\Summary;
use App\Models\UserPlan;
use App\Services\UsageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Services\QuizGenerationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;




class QuizController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the quizzes.
     */
    public function index(Request $request)
    {
        // Obtener todos los quizzes y pasarlos a la vista

        $user = Auth::user();

        // Obtener cuestionarios con filtros
        $query = Quiz::where('user_id', $user->id);

        // Aplicar filtros de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('difficulty_level', 'like', "%{$search}%")
                    ->orWhere('mode', 'like', "%{$search}%")
                    ->orWhere('num_questions', 'like', "%{$search}%");
            });
        }

        // Filtrar por dificultad
        if ($request->has('difficulty') && !empty($request->difficulty)) {
            $query->where('difficulty_level', $request->difficulty);
        }

        // Filtrar por modo
        if ($request->has('mode') && !empty($request->mode)) {
            $query->where('mode', $request->mode);
        }

        // Obtener cuestionarios paginados
//        $questionnaires = $query->with(['questionTypes', 'quizQuestions']) // Cargar tipos de preguntas y preguntas
//        ->withCount('quizQuestions') // Contar las preguntas asociadas
//        ->latest() // Ordenar por fecha de creación
//        ->paginate(5);

        // Obtener cuestionarios, ordenados primero por el uso más reciente y luego por la fecha de creación
        $questionnaires = $query->with(['questionTypes', 'quizQuestions', 'gameHistories']) // Cargar las relaciones necesarias
        ->withCount('quizQuestions')
            ->orderByDesc(GameHistory::selectRaw('MAX(created_at)') // Primero por la fecha más reciente de uso
            ->whereColumn('quiz_id', 'quizzes.id')
                ->limit(1)) // Selecciona el último uso para cada quiz
            ->orderByDesc('created_at') // Luego por la fecha de creación (más reciente)
            ->paginate(5);



        // Agregar la verificación de preguntas abiertas para cada cuestionario
        foreach ($questionnaires as $quiz) {
            $quiz->hasOpenQuestions = $quiz->questionTypes->contains(function($type) {
                return $type->name === 'Open Questions';  // Cambia 'Open' al nombre adecuado de tu tipo de pregunta
            });
        }

        // Obtener estadísticas del usuario
        $totalQuizzes = Quiz::where('user_id', $user->id)->count();

        $userId = $user->id;
        $uses =UsageService::calculateAvailableUses($userId);
        // Estos valores vendrían de la suscripción del usuario o configuración
        $availableCreations = $uses['quiz_creation']['remaining']?? 4;
        $studyModeUses = $uses['study_mode']['remaining']?? 5;
        $arenaModeUses = $uses['arena_mode']['remaining']?? 6;
//        $studyModeUses = 0;
//        $arenaModeUses = 0;
     //   $availableCreations=0;


        $currentUserPlan = UserPlan::where('user_id', $userId)
            ->where('end_date', '>=', now()) // Solo los planes activos
            ->latest('end_date') // El más reciente
            ->first();

        // Si no se encuentra un plan activo
        if (!$currentUserPlan) {
            // Puedes devolver valores por defecto o manejar el error según lo necesites
           // return response()->json(['error' => 'No active plan found for the user'], 400);
        }

        // Ahora obtenemos los datos del plan desde la tabla `plans` (suponiendo que tienes la relación 'plan')
        $plan = $currentUserPlan->plan; // Esto asume que tienes la relación 'plan' definida en tu modelo `UserPlan`

        // Extraer los límites del plan
        $planLimits = [
            'max_questions' => $plan->max_questions ?? 10,
            'pdf_files'     => $plan->pdf_files ?? 0,
            'urls'          => $plan->urls ?? 0,
            'text_limit'    => $plan->text_limit ?? 1000,
        ];


        return view('quizzes.index', compact(
            'questionnaires',
            'totalQuizzes',
            'availableCreations',
            'studyModeUses',
            'arenaModeUses',
            'planLimits'
        ));
    }

    /**
     * Show the form for creating a new quiz.
     */
    public function create()
    {
        // Mostrar el formulario para crear un nuevo quiz
        return view('quizzes.create');
    }

    /**
     * Store a newly created quiz in storage.
     */
//    public function store(StoreQuizRequest $request)
//    {
//        // Crear un nuevo quiz con los datos validados
//        $quiz = Quiz::create($request->validated());
//
//        // Redirigir a la lista de quizzes con un mensaje de éxito
//        return redirect()->route('quizzes.index')->with('success', 'Quiz creado exitosamente.');
//    }

    public function store(StoreQuizRequest $request)
    {
        $user = Auth::user();
        //----------con esto FUNCIONO ORGINALMENTE
        //$data = $request->validated(); // Obtiene solo los campos validados

        // Primero, guardamos el quiz con los datos proporcionados para mantener el registro
       // $data['user_id'] = Auth::id(); // Usando el ID del usuario autenticado
       //----------
        $validated = $request->validated();
        $content = '';

        if ($request->filled('topic')) {
            $content .= "Topic: " . $request->input('topic') . "\n\n";
        }

        if ($request->hasFile('pdf_file')) {
            $parser = new Parser();
            $pdfContents = [];

            // Recorrer todos los archivos PDF cargados
            foreach ($request->file('pdf_file') as $pdf) {
                $pdfContent = $parser->parseFile($pdf->getPathname()); // Extrae el contenido del archivo PDF
                $text = $pdfContent->getText(); // Obtén el texto completo
                if (!empty($text)) {
                    // Concatenar el contenido a la variable $content
                    $content .= "\n\n--- PDF Content ---\n" . $text;
                } else {
                    Log::warning("No text extracted from PDF.");
                }
            }

        }

        if ($request->filled('urls')) {
           // $urls = explode("\n", $request->input('urls'));
            foreach ($request->input('urls') as $url) {
                $text = QuizGenerationService::fetchTextFromURL(trim($url));
                // Eliminar elementos no deseados como JavaScript, etiquetas innecesarias, etc.
                $text = strip_tags($text);  // Eliminar etiquetas HTML
                $text = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $text); // Eliminar JavaScript

                if (!empty($text)) {
                    $content .= "\n\n--- URL Content ---\n" . $text;
                } else {
                    Log::warning("No content extracted from URL: $url");
                }
            }
        }

        if ($request->filled('manual_text')) {
            $content .= "\n\n--- Manual Text ---\n" . $request->input('manual_text');
        }

      //  $content = Str::limit($content, 4000); // Límite de caracteres para ahorrar tokens

        // Crear quiz en base
        $quiz = Quiz::create([
            'title' => $request->input('title') ?? 'Generated Quiz',
            'num_questions' => $validated['num_questions'],
            'difficulty_level' => $validated['difficulty_level'],
            'mode' => $validated['mode'],
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        QuizCreation::create([
            'user_id' =>  auth()->id(),
        ]);

        //  Log::info("Content before cleaning ->" . $content);

// LIMPIEZA FINAL
        $content = QuizGenerationService::cleanContent($content);
        Log::info("Content after cleaning ->" . $content);

        if (is_null($content) || trim($content) === '') {
            Log::error("generateWithGemini: Content is empty or null");
            throw new \InvalidArgumentException('No content provided for quiz generation.');
        }

        session(['quiz_content' => $content,
            'quiz_question_types' =>  $request->input('question_types', [])]);

        // Redirigir a vista de espera mientras se procesa
        return view('quizzes.wait', compact('quiz'));

        //----con esto funciono
//        $quiz = Quiz::create($data);
//        return redirect()->route('quizzes.index')->with('success', 'Quiz creado exitosamente.');
        //-----
        // Redirigir al usuario a la vista de espera
       // return redirect()->route('quizzes.wait', ['quiz' => $quiz->id]);
    }

    public function process(Request $request, Quiz $quiz)
    {
       // $content = $request->input('content');
        $content = session('quiz_content');
        $questionTypes = session('quiz_question_types', []);


        if (is_null($content) || trim($content) === '') {
            Log::error("No content found for quiz generation (session).");
            return response()->json(['status' => 'error', 'message' => 'No content available for generation.'], 400);
        }

        if (empty($questionTypes)) {
            Log::error("Missing question types in session");
            throw new \Exception('No question types provided for distribution.');
        }


        // Si el contenido es muy largo, resumir con Hugging Face
        $maxLength = 10000; // límite de caracteres
        if (strlen($content) > $maxLength) {
            Log::info("Maximum length exceeded for quiz generation.");
            $content = QuizGenerationService::summarizeWithGemini($content);
            Log::info("Summarized content:".$content);
        }

        // Llama al generador según config
       // $aiProvider = config('services.ai.provider', 'openai');
//        $questions = $aiProvider === 'gemini'
//            ? QuizGenerationService::generateWithGemini($content, $quiz)
//            : QuizGenerationService::generateWithOpenAI($content, $quiz);
        $success = QuizGenerationService::generateWithGemini($content, $quiz, $questionTypes);

        if (!$success) {
            Log::error("Failed to generate and store quiz properly.");
            return response()->json(['success' => false, 'message' => 'Error generating and saving the quiz.'], 500);
        }

        return response()->json(['success' => true, 'message' => 'Questions generated successfully.']);
    }






    /**
     * Display the specified quiz.
     */
    public function show(Quiz $quiz)
    {
        $this->authorize('view', $quiz);

        //ESTO ESTA MAL
        $quiz->oad('quizQuestions.quizAnswers');

        return view('$quizzes.show', compact('quiz'));
    }

    /**
     * Show the form for editing the specified quiz.
     */
    public function edit(Quiz $quiz)
    {
        // Mostrar el formulario para editar el quiz
        $this->authorize('update', $quiz);
        //ESTO ESTA MAL
        $quiz->load('quizQuestions.quizAnswers');

        return view('quizzes.edit', compact('quiz'));
    }

    /**
     * Update the specified quiz in storage.
     */
    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz);
        // Actualizar los datos del quiz con los datos validados
        $quiz->update($request->validated());

        // Redirigir a la lista de quizzes con un mensaje de éxito
        return redirect()->route('quizzes.index')->with('success', 'Quiz actualizado exitosamente.');
    }

    /**
     * Remove the specified quiz from storage.
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorize('delete', $quiz);
        // Eliminar el quiz
        $quiz->delete();

        // Redirigir a la lista de quizzes con un mensaje de éxito
        return redirect()->route('quizzes.index')->with('success', 'Quiz eliminado exitosamente.');
    }


    public function play(Quiz $questionnaire, $mode)
    {
        $this->authorize('play', $questionnaire);

        $user = Auth::user();
        $uses =UsageService::calculateAvailableUses($user->id);

        $studyModeUses = $uses['study_mode']['remaining']?? 5;
        $arenaModeUses = $uses['arena_mode']['remaining']?? 6;


        // Verificar si el usuario tiene usos disponibles para el modo seleccionado
        if ($mode === 'study' && ($studyModeUses) <= 0) {
            return redirect()->route('questionnaires.show', $questionnaire)
                ->with('error', 'No tienes usos de Modo Estudio disponibles.');
        }

        if ($mode === 'arena' && ($arenaModeUses) <= 0) {
            return redirect()->route('quizzes.show', $questionnaire)
                ->with('error', 'No tienes usos de Modo Arena disponibles.');
        }

        // ESTO ESTA MAL
        $questionnaire->load('quizQuestions.quizAnswers');

        // Reducir el contador de usos disponibles según el modo
//        if ($user->subscription) {
//            if ($mode === 'study') {
//                $user->subscription->decrement('remaining_study_uses');
//            } elseif ($mode === 'arena') {
//                $user->subscription->decrement('remaining_arena_uses');
//            }
//        }

        return view('quizzes.play', compact('questionnaire', 'mode'));
    }
}
