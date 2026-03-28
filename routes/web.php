<?php

use App\Http\Controllers\GameHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StoreController;
use App\Models\ArenaPlayer;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\XpPriceController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizQuestionController;
use App\Http\Controllers\ArenaGameController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\UrlValidationController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', [QuizController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');


//
//Route::get('/dashboard', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update-plan', [ProfileController::class, 'updatePlan'])->name('profile.updatePlan');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas para funcionalidades/tienda
    Route::get('/features', [FeatureController::class, 'index'])->name('features.index');
    Route::resource('quizzes', QuizController::class);
    Route::post('/quizzes/{quiz}/process', [QuizController::class, 'process'])->name('quizzes.process');
    Route::get('/quizzes/{quiz}/details', [QuizController::class, 'details'])->name('quizzes.details');
    Route::get('/download-quiz/{quiz_id}', [QuizController::class, 'downloadQuizAsPdf'])->name('quiz.downloadPdf');


    Route::resource('summaries', SummaryController::class);
    Route::post('/summaries/{summary}/process', [SummaryController::class, 'process'])->name('summaries.process');
    Route::get('/summaries/{summary}/details', [SummaryController::class, 'details'])->name('summaries.details');

    Route::get('/download-summary/{summary_id}', [SummaryController::class, 'downloadSummaryAsPdf'])->name('summaries.downloadPdf');


    Route::resource('arena', ArenaGameController::class);

    Route::post('/validate-url', [UrlValidationController::class, 'validate']);


    /////PLAY MODES
    Route::get('/quizzes/play/{questionnaire}', [GameHistoryController::class, 'play'])->name('quizzes.play');
    // En routes/web.php
    Route::post('/quizzes/submit-quiz-mode/{questionnaire}', [GameHistoryController::class, 'submitQuizMode'])->name('quizzes.submitQuizMode');
    Route::get('/quizzes/results', [GameHistoryController::class, 'showQuizResults'])->name('quizzes.showQuizResults');

    // Mostrar modo estudio
    Route::get('/quizzes/study/{quiz}', [GameHistoryController::class, 'study'])->name('quizzes.study');
// Enviar respuesta de una pregunta
    Route::post('/quizzes/study/answer/{quiz}', [GameHistoryController::class, 'submitStudyAnswer'])->name('quizzes.study.answer');
// Finalizar modo estudio
    Route::match(['get', 'post'], '/quizzes/study/finish/{quiz}', [GameHistoryController::class, 'finishStudyMode'])->name('quizzes.study.finish');
    Route::match(['get', 'post'], '/quizzes/play/exit/{quiz}', [GameHistoryController::class, 'exitMode'])->name('quizzes.exit');

    //MODO ARENA

    //HOST
    //chooseArenaQuiz

    Route::get('/quizzes/arena/choose-quiz', [ArenaGameController::class, 'chooseArenaQuiz'])->name('quizzes.choose-quiz');
    Route::get('/quizzes/arena/start/{quiz}', [GameHistoryController::class, 'startArenaGame'])->name('arena.startQuiz');


    //ENTRAR Y SALIR
    Route::post('/join-arena', [ArenaGameController::class, 'joinGame'])->name('arena.join');
    Route::post('/arena/{arena}/remove-player', [ArenaGameController::class, 'removePlayer'])->name('arena.removePlayer');


//    Route::get('/arena/waiting', function () {
//        return view('quizzes.waiting');
//    })->name('arena.waiting');
    Route::get('/arena/waiting', function () {
        $playerId = session('arena_player_id');
        $arenaGameId = session('arena_game_id');
        $pin = session('arena_game_pin');
        $score = 0;

        $player = \App\Models\ArenaPlayer::find($playerId);

        return view('quizzes.waiting', compact('player', 'arenaGameId', 'pin', 'score'));
    })->name('arena.waiting');


    ////POLLING ARENA:

    //LANZAR EVENTO GAME STARTED
    Route::post('/arena/{arenaGameId}/start-game', [GameHistoryController::class, 'startGame']);
    // Devuelve lista de jugadores

    Route::get('/arena/{id}/players', function ($id) {
        $players = ArenaPlayer::with('user')
            ->where('arena_game_id', $id)
            ->where('is_host', false)
            ->get();

        return response()->json([
            'players' => $players->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'user' => $p->user?->only(['id', 'name', 'email']),
            ])
        ]);
    });

// Devuelve estado del juego
    Route::get('/arena/{id}/status', function ($id) {
        $arena = \App\Models\ArenaGame::findOrFail($id);
        return response()->json(['status' => $arena->status]);
    });

    //MOSTRR PREGUTNAS
    Route::get('/arena/host/{arenaGameId}', [GameHistoryController::class, 'showHostView'])->name('arena.host.view');
    Route::get('/arena/play/{arenaGameId}', [ArenaGameController::class, 'showPlayerView'])->name('arena.player.view');

    //HOST
    Route::get('/arena/{arena}/question-status', [GameHistoryController::class, 'checkQuestionStatus']);
    Route::get('/arena/{arena}/question-summary', [GameHistoryController::class, 'questionSummary']);

    Route::post('/arena/{arenaGame}/next-question/{question}', [GameHistoryController::class, 'nextQuestion'])->name('arena.next-question');
    Route::post('/arena/{arenaGameId}/finish-game', [GameHistoryController::class, 'finishGame'])->name('arena.finish_game');

    //PALYERS

    Route::get('/arena/{arenaGameId}/players-answered', [ArenaGameController::class, 'playersAnsweredCount']);
    Route::get('/arena/{arenaGame}/question/{question}/result/{score}', [ArenaGameController::class, 'showQuestionResult'])->name('arena.show_question_result');
    // web.php
    Route::get('/arena/{arenaGame}/question/{question}/check-status', [ArenaGameController::class, 'checkResultStatus'])->name('arena.check_result_status');

    Route::get('/arena/{player}/current-question', [ArenaGameController::class, 'getCurrentQuestion']);

    Route::post('/arena/{arenaGame}/update-answer', [ArenaGameController::class, 'updatePlayerAnswer'])
        ->name('arena.update_player_answer');

    ///ME RENID, ENTONCES ESTOE S LO DE SHOP:
    Route::get('/xp-store', [StoreController::class, 'index'])->name('xp.store');
    Route::post('/xp-store/purchase-feature', [StoreController::class, 'purchaseFeature'])->name('xp.purchaseFeature');
    Route::post('/xp-store/purchase-package', [StoreController::class, 'purchasePackage'])->name('xp.purchasePackage');

    //Route::get('/play', [QuizController::class, 'play'])->name('features.index');

    //Route::resource('quiz-questions', QuizQuestionController::class);
    //Route::resource('quiz-answers', QuizAnswerController::class);
    //Route::apiResource('game-histories', GameHistoryController::class);
    //Route::apiResource('arena-games', ArenaGameController::class);

    //Route::get('/features', [FeatureController::class, 'index']);
    //Route::post('/user/{userId}/add-xp', [XpPriceController::class, 'addXpToUser']);

});

require __DIR__.'/auth.php';
