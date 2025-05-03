<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Welcome to Arena Mode</h1>
        </div>
    </x-slot>
    <div class="container text-center">
        <h1 class="text-3xl font-bold mb-4">Waiting for the game to start...</h1>
        <p class="text-lg text-gray-600">Do not close the window.</p>
    </div>

    <script>
        const quizId = '{{ $quizId }}';

        window.Echo.channel(`quiz.${quizId}`)
            .listen('GameStarted', () => {
                window.location.href = `/arena/play/${quizId}`;
            })
            .listen('QuestionSent', (e) => {
                window.location.href = `/arena/play/${quizId}/question/${e.question.id}`;
            })
            .listen('ShowPodium', () => {
                window.location.href = `/arena/${quizId}/podium`;
            });
    </script>

</x-app-layout>
