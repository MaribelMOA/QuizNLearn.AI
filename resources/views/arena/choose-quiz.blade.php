<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Arena Mode Quizzes') }}
        </h2>
    </x-slot>
    <div class="container">

        <div class="mb-3">
            <a href="#" class="btn btn-primary">
                Crear un nuevo Cuestionario
            </a>
        </div>

        @if($questionnaires->isEmpty())
            <div class="alert alert-info">
                No tienes cuestionarios en modo Arena. ¡Crea uno nuevo!
            </div>
        @else
            <div class="row">
                @foreach($questionnaires as $quiz)
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>{{ $quiz->title }}</h4>
                            </div>
                            <div class="card-body">
                                <p><strong>Número de preguntas:</strong> {{ $quiz->num_questions }}</p>
                                <p><strong>Dificultad:</strong> {{ $quiz->difficulty_level }}</p>
                                <p><strong>Fecha de creación:</strong> {{ $quiz->created_at->format('d/m/Y') }}</p>

                                <div class="mb-2">
                                    <a href="{{ route('arena.startQuiz', $quiz->id) }}" class="btn btn-success">
                                        Jugar
                                    </a>
                                </div>

                                @if($quiz->hasOpenQuestions)
                                    <div class="alert alert-warning">
                                        Este cuestionario contiene preguntas abiertas.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $questionnaires->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
