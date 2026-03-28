<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\StoreSummaryRequest;
use App\Http\Requests\UpdateSummaryRequest;
use App\Models\GameHistory;
use App\Models\Quiz;
use App\Models\QuizCreation;
use App\Models\Summary;

use App\Models\SummaryCreation;
use App\Models\UserPlan;
use App\Services\QuizGenerationService;
use App\Services\UsageService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;

class SummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Obtener todos los quizzes y pasarlos a la vista

        $user = Auth::user();

        // Obtener cuestionarios con filtros
        $query = Summary::where('user_id', $user->id);

        // Aplicar filtros de búsqueda
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }


        // Obtener cuestionarios, ordenados primero por el uso más reciente y luego por la fecha de creación
//        $summaries = Summary::where('user_id', $user->id)
//            ->orderBy('created_at', 'desc')
//            ->paginate(5);
        $summaries = $query
            ->orderBy('created_at', 'desc')
            ->paginate(5);






        // Obtener estadísticas del usuario
        $totalSummaries = Summary::where('user_id', $user->id)->count();

        $userId = $user->id;
        $uses =UsageService::calculateAvailableUses($userId);
        // Estos valores vendrían de la suscripción del usuario o configuración
        $availableCreations = $uses['summary_creation']['remaining']?? 0;


        $currentUserPlan = UserPlan::where('user_id', $userId)
            ->where('end_date', '>=', now()) // Solo los planes activos
            ->latest('end_date') // El más reciente
            ->first();


        // Ahora obtenemos los datos del plan desde la tabla `plans` (suponiendo que tienes la relación 'plan')
        $plan = $currentUserPlan->plan; // Esto asume que tienes la relación 'plan' definida en tu modelo `UserPlan`

        // Extraer los límites del plan
        $planLimits = [
            'max_questions' => $plan->max_questions ?? 10,
            'pdf_files'     => $plan->pdf_files ?? 0,
            'urls'          => $plan->urls ?? 0,
            'text_limit'    => $plan->text_limit ?? 1000,
        ];


        return view('summaries.index', compact(
            'summaries',
            'totalSummaries',
            'availableCreations',
            'planLimits'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('summaries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSummaryRequest $request)
    {
        $user = Auth::user();
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
        $summary = Summary::create([
            'user_id' => Auth::id(),
            'title' => $request->input('title') ?? 'Generated Quiz',
            'content' => "pending",
            'created_at' => now(),
        ]);

        SummaryCreation::create([
            'user_id' =>  auth()->id(),
        ]);


        $content = QuizGenerationService::cleanContent($content);
       // Log::info("Content after cleaning ->" . $content);

        if (is_null($content) || trim($content) === '') {
            Log::error("generateWithGemini: Content is empty or null");
            $summary->delete();
            throw new \InvalidArgumentException('No content provided for quiz generation.');
        }

        session(['summary_content' => $content]);

        // Redirigir a vista de espera mientras se procesa
        return view('summaries.wait', compact('summary'));

    }

    public function process(Request $request, Summary $summary)
    {
        // $content = $request->input('content');
        $content = session('summary_content');


        if (is_null($content) || trim($content) === '') {
            Log::error("No content found for summary generation (session).");
            $summary->delete();
            return response()->json(['status' => 'error', 'message' => 'No content available for generation.'], 400);
        }




        $content = QuizGenerationService::summarizeWithGemini($content);



        if (!$content) {
            Log::error("Failed to generate summary properly.");
            $summary->delete();
            return response()->json(['success' => false, 'message' => 'Error generating the summary.'], 500);
        }
        $summary->update(['content' => $content]);

        return response()->json(['success' => true, 'message' => 'Summary generated successfully.']);
    }

    /**
     * Display the specified quiz detaile
     */
    public function details(Summary $summary)
    {
        $content=$summary->content;
        // Pasar los valores a la vista usando compact
        return view('summaries.details', compact(
            'summary',          // Pasa el objeto quiz completo
            'content',      // Pasa el valor de avg_score calculado

        ));
    }

    public function downloadSummaryAsPdf($summary_id)
    {
        // Obtener el quiz junto con las preguntas y respuestas
        $summary = Summary::findOrFail($summary_id);


        // Generar el PDF usando los datos y pasarlos a la vista
        $pdf = PDF::loadHTML(QuizGenerationService::generateSummaryPdfContent($summary));

        // Descargar el archivo PDF con el nombre del quiz
        return $pdf->download('summary_' . $summary->title . '.pdf');
    }

    /**
     * Display the specified resource.
     */
    public function show(Summary $summary)
    {
       // $this->authorize('view', $summary);
        return view('summaries.show', compact('summary'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Summary $summary)
    {
        //$this->authorize('update', $summary);
        return view('summaries.edit', compact('summary'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSummaryRequest $request, Summary $summary)
    {
       // $this->authorize('update', $summary);

        $summary->update($request->validated());

        return redirect()->route('summaries.index')->with('success', 'Resumen actualizado.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Summary $summary)
    {
      //  $this->authorize('delete', $summary);
        $summary->delete();

        return redirect()->route('summaries.index')->with('success', 'Resumen eliminado.');

    }
}
