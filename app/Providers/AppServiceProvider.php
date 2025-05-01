<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            // Verificar si la vista actual es una de quiz
            if (request()->is('quizzes/*/play') || request()->is('quizzes/play/*') || request()->is('quizzes/submit-quiz-mode/*')
                || request()->is('quizzes/study/*')
                || request()->is('quizzes/study/answer/*')
                || request()->is('quizzes/study/finish/*')
                || request()->is('quizzes/play/exit/*')
            ) {
                $view->with('hideNavigation', true);
                $view->with('isQuizPage', true);
            }else {
                $view->with('isQuizPage', false);
            }
        });
    }
}
