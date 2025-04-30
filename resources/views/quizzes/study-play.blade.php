<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-gray-800">{{ $quiz->title }}</h2>
            </div>

            <div class="flex items-center space-x-4">
                <p id="question-counter" class="text-sm text-gray-600">Total quesitions: {{ $totalQuestions }}</p>
                <div class="flex items-center text-lg text-gray-700 font-bold">
                    ⏱️ <span id="timer">00:00</span>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto p-6 mt-8 bg-white rounded-md shadow-md">
        <form id="study-form" method="POST">
            @csrf
            <input type="hidden" name="question_id" value="{{ $question->id }}">

            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-4">{{ $question->question_text }}</h3>

                @if ($question->type->name === 'multiple_choice' || $question->type->name === 'true_or_false')
                    <div class="space-y-3">
                        @foreach ($question->quizQuestionAnswers as $answer)
                            <label class="flex items-center space-x-2">
                                <input type="radio" name="answer" value="{{ $answer->id }}" class="text-indigo-600">
                                <span class="text-gray-700">{{ $answer->answer_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @elseif ($question->type->name === 'open_question')
                    <textarea name="answer" rows="3" class="w-full border-gray-300 rounded-md" placeholder="Tu respuesta..."></textarea>
                @endif
            </div>

            <div class="flex justify-end space-x-4">
                <button  id="submit-button" type="button" onclick="submitAnswer()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded">
                    Submit answer
                </button>
            </div>
        </form>

        <div id="feedback" class="mt-6 hidden">
            <p class="text-lg font-medium" id="feedback-text"></p>
            <button id="next-question"
                    class="mt-4 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded hidden"
                    onclick="loadNextQuestion()">
                {{ $isLastQuestion ? 'Finish' : 'Next' }}
            </button>
        </div>

        <div id="finish-section" class="mt-6 hidden">
            <form action="{{ route('quizzes.study.finish', $quiz->id) }}" method="POST">
                @csrf
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded text-lg">
                    Finalizar Estudio
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        {{--let totalSeconds = {{ $elapsedSeconds ?? 0 }};--}}
        {{--const timer = document.getElementById('timer');--}}

        {{--setInterval(() => {--}}
        {{--    totalSeconds++;--}}
        {{--    const minutes = Math.floor(totalSeconds / 60);--}}
        {{--    const seconds = totalSeconds % 60;--}}
        {{--    timer.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;--}}
        {{--}, 1000);--}}
        let totalSeconds = sessionStorage.getItem("studyTimer") || 0;
        totalSeconds = parseInt(totalSeconds);

        function formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
        }

        setInterval(() => {
            totalSeconds++;
            sessionStorage.setItem("studyTimer", totalSeconds);
            document.getElementById("timer").textContent = formatTime(totalSeconds);
        }, 1000);

        function submitAnswer() {
            const submitBtn = document.getElementById('submit-button');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

            const form = document.getElementById('study-form');
            const formData = new FormData(form);
            formData.append("elapsed_time", totalSeconds);

            axios.post("{{ route('quizzes.study.answer', $quiz->id) }}", formData)
                .then(response => {
                    const feedback = response.data.feedback;
                    const correct = response.data.correct;

                    document.getElementById('feedback').classList.remove('hidden');
                    document.getElementById('feedback-text').innerText = feedback;

                    if (@json($isLastQuestion)) {
                        document.getElementById('finish-section').classList.remove('hidden');
                        document.getElementById('next-question').classList.add('hidden');
                    } else {
                        document.getElementById('next-question').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    alert('You must answer the question before moving on.');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

                });
        }

        function loadNextQuestion() {
            window.location.href = "{{ route('quizzes.study', $quiz->id) }}";
            //location.reload(); // Cargar siguiente pregunta (actualizamos sesión)
        }
    </script>

</x-app-layout>


