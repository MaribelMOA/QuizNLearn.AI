<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuizRequest;
use App\Http\Requests\UpdateQuizRequest;
use App\Models\Quiz;
use App\models\GameHistory;
use App\Models\Summary;
use App\Services\UsageService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



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

        return view('quizzes.index', compact(
            'questionnaires',
            'totalQuizzes',
            'availableCreations',
            'studyModeUses',
            'arenaModeUses'
        ));  }

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
        $uses =UsageService::calculateAvailableUses($user->id);
        // Estos valores vendrían de la suscripción del usuario o configuración
        $availableCreations = $uses['quiz_creation']['remaining']?? 4;
        if($availableCreations>0){
            return redirect()->route('quizzes.index')
                ->with('error', 'No tienes creaciones de cuestionarios disponibles.');
        }

        // Primero, guardamos el quiz con los datos proporcionados para mantener el registro
        $quiz = Quiz::create($request->validated());

        //AGREGAR LO DE REDUCIR USOOOOOOOOS
       // DIGO, QUIZA NO SE OCUPE
        // Redirigir al usuario a la vista de espera
        return redirect()->route('quizzes.wait', ['quiz' => $quiz->id]);
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
