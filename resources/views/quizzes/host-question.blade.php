<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Arena Mode</h1>
        </div>
    </x-slot>
    <div class="container text-center">
        <h1 class="text-2xl font-bold mb-4" id="question-text">{{ $question->text ?? 'Waiting for question...' }}</h1>

        <div class="grid grid-cols-2 gap-4" id="answers-container">
            {{-- Opciones de respuesta se inyectarán vía JS --}}
        </div>
        <p>Respuestas recibidas: <span id="answer-count">0</span></p>


        <div id="timer" class="text-xl mt-6">Tiempo restante: <span id="time-left">10</span>s</div>
    </div>

    <script>
        const quizId = '{{ $question->quiz_id }}';

        window.Echo.channel(`quiz.${quizId}`)
            .listen('AnswerReceived', (e) => {
                console.log(`Jugador respondió: ${e.user.nickname}`);
                // Aquí podrías mostrar un contador de respuestas si quieres
            });

        let answerCount = 0;

        window.Echo.channel(`quiz.${quizId}`)
            .listen('AnswerReceived', (e) => {
                answerCount++;
                document.getElementById('answer-count').textContent = answerCount;
            });
    </script>
</x-app-layout>
