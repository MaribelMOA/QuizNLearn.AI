<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Arena Mode</h1>
        </div>
    </x-slot>
    <div class="container">
        <h2>Cuestionario: {{ $quiz->title }}</h2>
        <h4>Pregunta {{ $questionNumber }} / {{ $totalQuestions }}</h4>

        <div id="timer" class="alert alert-info">
            Tiempo restante: <span id="countdown">{{ $timeLimit }}</span> segundos
        </div>

        <div class="alert alert-secondary">
            <strong>Jugadores que han respondido:</strong> <span id="answered-count">{{ $playersAnswered }}</span> / {{ $totalPlayers }}
        </div>

        <hr>
        <div class="mb-3">
            <h5>{{ $question->question_text }}</h5>
            <ul class="list-group mt-3">
                @foreach($question->quizQuestionAnswers as $option)
                    <li class="list-group-item">{{ $option->answer_text }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <script>
        let timeLeft = {{ $timeLimit }};
        const countdownEl = document.getElementById('countdown');

        const interval = setInterval(() => {
            timeLeft--;
            countdownEl.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(interval);
            }
        }, 1000);
    </script>
</x-app-layout>
