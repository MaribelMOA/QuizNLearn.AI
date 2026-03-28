<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 py-6">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Arena Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="flex items-center mb-4 md:mb-0">
                            <div class="bg-white p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-white">Arena Mode</h1>
                                <p class="text-purple-200">Host View</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">

                            <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 text-white">
                                <span class="font-medium">Game PIN:</span>
                                <span class="text-xl font-bold tracking-wider">{{ $pin ?? 'ABC123' }}</span>
                            </div>
{{--                            <a href="{{ route('arena.finish_game') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg font-medium hover:bg-purple-50 transition-colors" onclick="return confirm('Are you sure you want to finish the game? Your progress will be lost.'>--}}
{{--                                End Game--}}
{{--                            </a>--}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Quiz Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Quiz Title Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-indigo-100 px-4 py-2 border-b border-indigo-200">
                            <h3 class="font-medium text-indigo-800">Quiz</h3>
                        </div>
                        <div class="p-4">
                            <h2 class="text-xl font-bold text-gray-800 mb-2">{{ $quiz->title }}</h2>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Question {{ $questionIndex}} of {{ $totalQuestions }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Timer Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-red-100 px-4 py-2 border-b border-red-200">
                            <h3 class="font-medium text-red-800">Time Remaining</h3>
                        </div>
                        <div class="p-6 flex justify-center">
                            <div class="relative w-32 h-32">
                                <!-- Timer Circle Background -->
                                <svg class="w-full h-full" viewBox="0 0 100 100">
                                    <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" />
                                    <!-- Timer Progress Circle (will be animated with JS) -->
                                    <circle
                                        id="timer-circle"
                                        class="text-red-500 transition-all duration-1000 ease-linear"
                                        stroke-width="10"
                                        stroke="currentColor"
                                        stroke-linecap="round"
                                        stroke-dasharray="283"
                                        stroke-dashoffset="0"
                                        fill="transparent"
                                        r="45"
                                        cx="50"
                                        cy="50"
                                    />
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <span id="countdown" class="text-4xl font-bold text-red-600">{{ $timeLimit }}</span>
                                        <span class="block text-sm text-gray-500">seconds</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Players Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <div class="bg-green-100 px-4 py-2 border-b border-green-200">
                            <h3 class="font-medium text-green-800">Players</h3>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-gray-600">Answered:</span>
                                <span id="answered-count" class="font-bold text-green-600">{{ $playersAnswered }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Total Players:</span>
                                <span id="total-players" class="font-bold text-gray-800">{{ $totalPlayers -1}}</span>

                            </div>

                            <!-- Progress Bar -->
                            <div class="mt-4 bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div
                                    id="players-progress"
                                    class="bg-green-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                                    style="width: {{ ($playersAnswered / max(1, $totalPlayers-1)) * 100 }}%"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Question and Answers -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-4">
                            <h2 class="text-xl font-bold text-white">{{ $questionIndex }}. Current Question</h2>
                        </div>

                        <div class="p-6">
                            <div class="bg-purple-50 border border-purple-100 rounded-lg p-4 mb-6">
                                <h3 class="text-xl font-semibold text-gray-800">{{ $question->question_text }}</h3>
                            </div>

                            @php
                                $colors = [
                                    'bg-red-500 border-red-600 hover:border-red-700 text-white',
                                    'bg-green-500 border-green-600 hover:border-green-700 text-white',
                                    'bg-yellow-500 border-yellow-600 hover:border-yellow-700 text-white',
                                    'bg-blue-500 border-blue-600 hover:border-blue-700 text-white',
                                ];
                            @endphp

                            <div class="space-y-3">
                                @foreach($question->quizQuestionAnswers as $index => $option)
                                    @php
                                        $colorClass = $colors[$index % count($colors)];
                                    @endphp
                                    <div class="rounded-lg p-4 transition-colors flex items-center {{ $colorClass }}">
                                        <div class="w-8 h-8 rounded-full bg-white bg-opacity-30 text-current flex items-center justify-center font-bold mr-3">
                                            {{ chr(65 + $index) }}
                                        </div>
                                        <span class="text-current">{{ $option->answer_text }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

{{--                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">--}}
{{--                            <div class="flex justify-between items-center">--}}


{{--                                <button id="next-question" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">--}}
{{--                                    Next--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />--}}
{{--                                    </svg>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        {{--let timeLeft = {{ $timeLimit }};--}}
        {{--const countdownEl = document.getElementById('countdown');--}}

        {{--const interval = setInterval(() => {--}}
        {{--    timeLeft--;--}}
        {{--    countdownEl.textContent = timeLeft;--}}

        {{--    if (timeLeft <= 0) {--}}
        {{--        clearInterval(interval);--}}
        {{--    }--}}
        {{--}, 1000);--}}
        document.addEventListener('DOMContentLoaded', function() {
            const arenaGameId = @json($arenaGameId);
            let checkInterval = setInterval(() => {
                fetch('/arena/{{ $arenaGameId }}/question-status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.finished) {
                            clearInterval(checkInterval);
                            window.location.href = `/arena/{{ $arenaGameId }}/question-summary`;
                        }
                    });
            }, 3000);  // checa cada 3 segundos


            // Timer functionality
            const countdownElement = document.getElementById('countdown');
            const timerCircle = document.getElementById('timer-circle');
            const playersProgress = document.getElementById('players-progress');
            const answeredCountElement = document.getElementById('answered-count');

            let timeLeft = parseInt(countdownElement.textContent);
            const totalTime = timeLeft;
            const circumference = 283; // 2 * π * r where r=45

            const timer = setInterval(function() {
                timeLeft--;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timeLeft = 0;
                    window.location.href = `/arena/{{ $arenaGameId }}/question-summary`;

                    // Here you would typically trigger the next question
                    // or show results via an AJAX call
                }

                // Update countdown text
                countdownElement.textContent = timeLeft;

                // Update timer circle
                const offset = circumference - (timeLeft / totalTime) * circumference;
                timerCircle.style.strokeDashoffset = offset;

                // Change color based on time remaining
                if (timeLeft <= 5) {
                    timerCircle.classList.remove('text-red-500');
                    timerCircle.classList.add('text-red-600');
                    countdownElement.classList.add('animate-pulse');
                }
            }, 1000);

            // Initialize the timer circle
            timerCircle.style.strokeDasharray = circumference;

            // Simulate players answering (for demo purposes)
            // In a real app, this would be updated via WebSockets or polling

           // const answeredCountElement = document.getElementById('answered-count');
            const totalPlayersElement = document.querySelector('[id="total-players"]') || null;
            //const playersProgress = document.getElementById('players-progress');

            function fetchPlayerAnswers() {
                fetch(`/arena/${arenaGameId}/players-answered`)
                    .then(response => response.json())
                    .then(data => {
                        const { answered, total } = data;

                        // Actualiza número de jugadores que han contestado
                        if (answeredCountElement) {
                            answeredCountElement.textContent = answered;
                        }

                        // Actualiza total de jugadores si tienes un span específico con ID
                        if (totalPlayersElement) {
                            totalPlayersElement.textContent = total;
                        }

                        // Actualiza barra de progreso
                        const percentage = total > 0 ? (answered / total) * 100 : 0;
                        if (playersProgress) {
                            playersProgress.style.width = `${percentage}%`;
                        }
                    });
            }

            // Llama cada 3 segundos
            setInterval(fetchPlayerAnswers, 3000);
            {{--let currentAnswered = {{ $playersAnswered }};--}}
            {{--const totalPlayers = {{ $totalPlayers }};--}}

            {{--const playerSimulation = setInterval(function() {--}}
            {{--    if (currentAnswered < totalPlayers && Math.random() > 0.7) {--}}
            {{--        currentAnswered++;--}}
            {{--        answeredCountElement.textContent = currentAnswered;--}}

            {{--        // Update progress bar--}}
            {{--        const percentage = (currentAnswered / totalPlayers) * 100;--}}
            {{--        playersProgress.style.width = percentage + '%';--}}

            {{--        // If all players have answered, clear the interval--}}
            {{--        if (currentAnswered >= totalPlayers) {--}}
            {{--            clearInterval(playerSimulation);--}}
            {{--        }--}}
            {{--    }--}}
            {{--}, 1500);--}}


            // document.getElementById('next-question').addEventListener('click', function() {
            //     // Navigate to next question
            //     console.log('Navigate to next question');
            // });


        });
    </script>
</x-app-layout>
