<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 py-6">
        <div class="max-w-lg mx-auto px-4">
            <!-- Game Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white p-2 rounded-full mr-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h1 class="text-xl font-bold text-white">Arena Mode</h1>
                        </div>
                        <div class="text-white text-sm">
                            <span class="font-medium">{{ $quiz->title }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Game Stats -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <!-- Timer Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-4 flex items-center justify-center">
                        <div class="relative w-16 h-16">
                            <!-- Timer Circle Background -->
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle class="text-gray-200" stroke-width="10" stroke="currentColor" fill="transparent" r="45" cx="50" cy="50" />
                                <!-- Timer Progress Circle -->
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
                                    <span id="countdown" class="text-2xl font-bold text-red-600">{{ $timeLimit }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="ml-3">
                            <span class="block text-sm text-gray-500">Time Left</span>
                            <span class="block text-lg font-medium text-gray-800">seconds</span>
                        </div>
                    </div>
                </div>

                <!-- Players Card -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600 text-sm">Players Answered:</span>
                            <span id="answered-count" class="font-bold text-green-600">{{ $playersAnswered }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 text-sm">Total Players:</span>
                            <span class="font-bold text-gray-800">{{ $totalPlayers -1}}</span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-2 bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div
                                id="players-progress"
                                class="bg-green-600 h-2 rounded-full transition-all duration-500 ease-out"
                                style="width: {{ ($playersAnswered / max(1, $totalPlayers-1)) * 100 }}%"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question and Answers -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-4">
                    <h2 class="text-xl font-bold text-white">Question</h2>
                </div>

                <div class="p-6">
                    <div class="bg-purple-50 border border-purple-100 rounded-lg p-4 mb-6">
                        <h3 class="text-xl font-semibold text-gray-800">{{ $question->question_text }}</h3>
                    </div>

                    <form id="answer-form" method="POST" action="{{ route('arena.update_player_answer', $arenaGameId) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">

                        @php
                            $colors = [
                                'bg-red-500 border-red-600 hover:border-red-700 text-white',
                                'bg-green-500 border-green-600 hover:border-green-700 text-white',
                                'bg-yellow-500 border-yellow-600 hover:border-yellow-700 text-white',
                                'bg-blue-500 border-blue-600 hover:border-blue-700 text-white',
                            ];
                        @endphp

                        @foreach($question->quizQuestionAnswers as $index => $answer)
                            @php
                                $color = $colors[$index % count($colors)];
                            @endphp
                            <label class="block cursor-pointer">
                                <input type="radio" name="selected_answer_id" value="{{ $answer->id }}" class="peer sr-only">
                                <div class="border-2 {{ $color }} peer-checked:ring-2 peer-checked:ring-offset-2 peer-checked:ring-indigo-400 rounded-lg p-4 transition-colors flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-white text-black flex items-center justify-center font-bold mr-3">
                                        {{ chr(65 + $index) }}
                                    </div>
                                    <span class="peer-checked:font-semibold">{{ $answer->answer_text }}</span>
                                    <svg class="w-5 h-5 text-white ml-auto opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                            </label>
                        @endforeach
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>

            const arenaId = {{ $arenaGameId }};
            const questionId = {{ $question->id }};


            const interval = setInterval(() => {
            fetch(`/arena/${arenaId}/question/${questionId}/check-status`)
                .then(res => res.json())
                .then(data => {
                    console.log("Polling result:", data);

                    if (data.status === 'finished') {
                        clearInterval(interval);
                        Swal.fire({
                            title: 'Game Over!',
                            text: 'Thanks for playing!',
                            icon: 'info',
                            confirmButtonText: 'OK',
                        }).then(() => {
                            window.location.href = "{{ route('quizzes.index') }}";

                        });
                    }

                });
        }, 3000);


    document.addEventListener('DOMContentLoaded', function() {
            // Timer functionality
            const countdownElement = document.getElementById('countdown');
            const timerCircle = document.getElementById('timer-circle');
            const form = document.getElementById('answer-form');
            const selectedAnswerInput = document.getElementById('selected-answer-id'); // hidden input for answer

            console.log("Selected answer:",selectedAnswerInput);
            let timeLeft = parseInt(countdownElement.textContent);
            const totalTime = timeLeft;
            const circumference = 283; // 2 * π * r where r=45

            const timer = setInterval(function() {
                timeLeft--;

                if (timeLeft <= 0) {
                    clearInterval(timer);
                    timeLeft = 0;
                    form.submit(); // Auto-submit when time runs out
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

            // Add visual feedback when selecting an answer
            const radioInputs = document.querySelectorAll('input[type="radio"]');
            radioInputs.forEach(input => {
                input.addEventListener('change', function() {
                    console.log('Radio seleccionado:', this.name, '=', this.value); // <-- Aquí mostramos qué radio se eligió

                    // También mostramos todos los radios por si ayuda a depurar
                    const formData = new FormData(form);
                    for (let [key, value] of formData.entries()) {
                        console.log(`${key}: ${value}`);
                    }form.submit(); // autoenvía la respuesta cuando se selecciona

                });
            });



            /////POLLING PLAYER ANSWERD
            const answeredCount = document.getElementById('answered-count');
            const playersProgress = document.getElementById('players-progress');
            const arenaGameId = @json($arenaGameId); // Asegúrate que esta variable esté disponible

            function fetchPlayerAnswers() {
                fetch(`/arena/${arenaGameId}/players-answered`)
                    .then(response => response.json())
                    .then(data => {
                        const { answered, total } = data;
                        answeredCount.textContent = answered;

                        const percentage = total > 0 ? (answered / total) * 100 : 0;
                        playersProgress.style.width = `${percentage}%`;
                    });
            }

            setInterval(fetchPlayerAnswers, 3000); // cada 3 segundos


        });

    </script>
</x-app-layout>
