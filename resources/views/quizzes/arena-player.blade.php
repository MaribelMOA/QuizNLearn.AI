<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">Modo Arena - Jugador</h1>
    </x-slot>

    <div class="container mx-auto text-center">
        <div id="question-section" class="my-6">
            <h2 id="question-text" class="text-xl font-semibold mb-4">Cargando pregunta...</h2>
            <ul id="answer-options" class="mb-4">
                <!-- Opciones se inyectan por JS -->
            </ul>

            <div class="mb-4">
                <p><strong>Tiempo restante:</strong> <span id="countdown">20</span> segundos</p>
            </div>

            <div class="mb-4">
                <p>Respuestas recibidas: <span id="response-count">0</span></p>
            </div>
        </div>

        <div id="waiting-message" class="hidden">
            <p class="text-lg font-medium">Esperando la siguiente pregunta...</p>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="//{{ Request::getHost() }}:6001/socket.io/socket.io.js"></script>
        <script src="{{ mix('/js/app.js') }}"></script> <!-- Laravel Echo -->
        <script>
            const quizId = {{ $quiz->id }};
            let countdown = 20;
            let interval;
            let responseSent = false;

            const countdownEl = document.getElementById('countdown');
            const questionTextEl = document.getElementById('question-text');
            const answerOptionsEl = document.getElementById('answer-options');
            const responseCountEl = document.getElementById('response-count');

            function startCountdown() {
                countdown = 20;
                countdownEl.textContent = countdown;
                interval = setInterval(() => {
                    countdown--;
                    countdownEl.textContent = countdown;
                    if (countdown <= 0) {
                        clearInterval(interval);
                        if (!responseSent) {
                            responseSent = true;
                            sendAnswer(null); // Si no respondió, enviar vacío
                        }
                    }
                }, 1000);
            }

            function renderQuestion(question) {
                questionTextEl.textContent = question.question_text;
                answerOptionsEl.innerHTML = '';
                responseSent = false;
                responseCountEl.textContent = '0';

                question.answers.forEach(answer => {
                    const li = document.createElement('li');
                    li.classList.add('border', 'p-2', 'rounded', 'cursor-pointer', 'hover:bg-blue-100');
                    li.textContent = answer.answer_text;
                    li.onclick = () => {
                        if (!responseSent) {
                            sendAnswer(answer.id);
                            responseSent = true;
                        }
                    };
                    answerOptionsEl.appendChild(li);
                });

                startCountdown();
            }

            function sendAnswer(answerId) {
                axios.post(`/api/arena/${quizId}/submit-answer`, {
                    answer_id: answerId,
                }).then(res => {
                    document.getElementById('waiting-message').classList.remove('hidden');
                });
            }

            Echo.channel(`arena.quiz.${quizId}`)
                .listen('ArenaQuestionSent', (e) => {
                    document.getElementById('waiting-message').classList.add('hidden');
                    renderQuestion(e.question);
                })
                .listen('ArenaAnswerCountUpdated', (e) => {
                    responseCountEl.textContent = e.count;
                });
        </script>
    @endpush
</x-app-layout>
