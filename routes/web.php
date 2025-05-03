<?php

use App\Http\Controllers\GameHistoryController;
use App\Http\Controllers\ProfileController;
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


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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



    Route::prefix('/quizzes/arena')->group(function () {
      //  Route::get('/{quizId}/host-lobby', [GameHistoryController::class, 'hostLobby'])->name('quizzes.host-lobby');
        Route::get('/{quizId}/host-question', [GameHistoryController::class, 'hostQuestion'])->name('quizzes.host-question');
        Route::get('/{quizId}/waiting-room', [GameHistoryController::class, 'waitingRoom'])->name('quizzes.waiting-room');
        Route::get('/{quizId}/podium', [GameHistoryController::class, 'podium'])->name('quizzes.podium');
        Route::post('/{quizId}/start', [GameHistoryController::class, 'startGame'])->name('quizzes.start-game');

    });
   // Route::post('/{quizId}/start', [GameHistoryController::class, 'startGame'])->name('quizzes.start-game');

    Route::post('/join-arena', [ArenaGameController::class, 'joinGame'])->name('arena.join');

    Route::get('/arena/waiting', function () {
        return view('quizzes.waiting');
    })->name('arena.waiting');


    //Route::get('/play', [QuizController::class, 'play'])->name('features.index');

    //Route::resource('quiz-questions', QuizQuestionController::class);
    //Route::resource('quiz-answers', QuizAnswerController::class);
    //Route::apiResource('game-histories', GameHistoryController::class);
    //Route::apiResource('arena-games', ArenaGameController::class);

    //Route::get('/features', [FeatureController::class, 'index']);
    //Route::post('/user/{userId}/add-xp', [XpPriceController::class, 'addXpToUser']);

});

require __DIR__.'/auth.php';
