<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Arena Player Mode</h1>
        </div>
    </x-slot>
    <div class="container">
        <h3>{{ $quiz->title }}</h3>

        <div id="timer" class="alert alert-warning">
            Time: <span id="countdown">{{ $timeLimit }}</span> seconds
        </div>

        <div class="alert alert-secondary">
            <strong>Answers:</strong> <span id="answered-count">{{ $playersAnswered }}</span> / {{ $totalPlayers }}
        </div>

        <hr>
        <form id="answer-form" method="POST" action="{{ route('game.submitAnswer', [$gameId]) }}">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            <div class="mb-3">
                <h5>{{ $question->question_text }}</h5>
                @foreach($question->quizQuestionAnswers as $answer)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answer" id="answer{{ $answer->id }}" value="{{ $answer->id }}" required>
                        <label class="form-check-label" for="answer{{ $answer->id }}">
                            {{ $answer->answer_text }}
                        </label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary mt-3">Submit Answer</button>
        </form>

    </div>

    <script>
        let timeLeft = {{ $timeLimit }};
        const countdownEl = document.getElementById('countdown');
        const form = document.getElementById('answer-form');

        const interval = setInterval(() => {
            timeLeft--;
            countdownEl.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(interval);
                form.submit(); // Autoenvía si el tiempo acaba
            }
        }, 1000);
        {{--let countdown = {{ $questionTime }};--}}
        {{--const interval = setInterval(() => {--}}
        {{--    countdown--;--}}
        {{--    document.getElementById('countdown').innerText = countdown;--}}
        {{--    if (countdown <= 0) {--}}
        {{--        clearInterval(interval);--}}
        {{--        document.getElementById('answer-form').querySelector('button').disabled = true;--}}
        {{--    }--}}
        {{--}, 1000);--}}

        {{--document.getElementById('answer-form').addEventListener('submit', function (e) {--}}
        {{--    e.preventDefault();--}}

        {{--    const formData = new FormData(this);--}}
        {{--    fetch('{{ route('arena.player.submit', ['arenaGameId' => $arenaGameId]) }}', {--}}
        {{--        method: 'POST',--}}
        {{--        headers: {--}}
        {{--            'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}
        {{--        },--}}
        {{--        body: formData--}}
        {{--    })--}}
        {{--        .then(res => res.json())--}}
        {{--        .then(data => {--}}
        {{--            if (data.success) {--}}
        {{--                document.getElementById('feedback').innerHTML = data.feedback;--}}
        {{--                document.getElementById('answer-form').querySelector('button').disabled = true;--}}
        {{--            } else {--}}
        {{--                document.getElementById('feedback').innerHTML = 'Error al enviar respuesta.';--}}
        {{--            }--}}
        {{--        });--}}
        {{--});--}}

        {{--// Eliminar mensaje de espera cuando llega la pregunta--}}
        {{--Echo.join(`arena.{{ $arenaGameId }}`)--}}
        {{--    .listen('.new.question', (e) => {--}}
        {{--        window.location.reload(); // O mejor, renderiza la pregunta sin recargar--}}
        {{--    });--}}
    </script>
</x-app-layout>
