<x-app-layout >

    <x-slot name="header">

        <div class="flex justify-between items-center">
            <div class="flex flex-col">
                <h2 class="font-semibold text-3xl text-gray-800">
                    Title: "{{ $quiz->title }}"
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Level:
                    @php
                        $difficultyColors = [
                            'Easy' => 'bg-emerald-100 text-emerald-800',
                            'Medium' => 'bg-amber-100 text-amber-800',
                            'Hard' => 'bg-rose-100 text-rose-800',
                        ][$quiz->difficulty_level] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $difficultyColors }}">
                    {{ ucfirst($quiz->difficulty_level) }}
                </span>
                </p>
            </div>

            <div class="flex items-center space-x-6">

                <p id="answered-questions-counter" class="text-sm text-gray-500">{{ $answeredQuestions }} / {{ $totalQuestions }} answered</p>

                <!-- Cronómetro más grande y vistoso -->
                <div class="flex items-center text-gray-700 text-2xl font-semibold">
                    ⏱️ <span id="timer" class="ml-2">00:00</span>
                </div>

            </div>
        </div>
    </x-slot>

    <div class="py-6 ">

        <div class="max-w-4xl mx-auto p-6 bg-white shadow-md rounded-md mt-6">


            <!-- Formulario del Quiz -->
            <form action="{{ route('quizzes.submitQuizMode', $quiz->id) }}" method="POST" id="quiz-form">
                @csrf

                @foreach ($questions as $index => $question)
                    <div class="mb-6 border-b border-gray-200 pb-4">
                        <p class="font-medium text-gray-800 mb-3">
                            {{ $index + 1 }}. {{ $question->question_text }}
                        </p>

                        @if ($question->type->name === 'multiple_choice' || $question->type->name === 'true_or_false')
                            <div class="space-y-2">
                                @foreach ($question->quizQuestionAnswers as $answer)
                                    <label class="flex items-center space-x-2">
                                        <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" class="text-indigo-600" required>
                                        <span class="text-gray-700">{{ $answer->answer_text }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @elseif ($question->type->name === 'open_question')
                            <textarea name="answers[{{ $question->id }}]" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Your answer..." required ></textarea>
                        @endif
                    </div>
                @endforeach
                <input type="hidden" name="total_time_seconds" id="total_time_seconds">


                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-md transition-colors">
                        Submit Quiz
                    </button>
                </div>

            </form>
        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Cronómetro
        let seconds = 0;
        let minutes = 0;
        const timerElement = document.getElementById('timer');

        setInterval(() => {
            seconds++;
            if (seconds === 60) {
                minutes++;
                seconds = 0;
            }
            timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }, 1000);

        // Confirmar salir
        document.getElementById('close-quiz').addEventListener('click', function() {
            Swal.fire({
                title: 'Are you sure you want to exit?',
                text: "Your progress will be lost",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, exit',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('quizzes.index') }}";
                }
            });
        });

        // ---------------------
        // Contador de respuestas
        // ---------------------
        const form = document.getElementById('quiz-form');
        const counterElement = document.getElementById('answered-questions-counter');
        const totalQuestions = {{ $totalQuestions }};
        const answeredQuestions = new Set(); // Para evitar contar duplicados

        // Escuchamos los cambios en el formulario
        form.addEventListener('change', function(event) {
            const target = event.target;

            // Verificamos si el objetivo es un radio button o un textarea
            if ((target.type === 'radio' && target.name.startsWith('answers')) ||
                (target.tagName.toLowerCase() === 'textarea')) {

                // Obtener el ID de la pregunta desde el name del input (answers[questionId])
                const questionId = target.name.match(/\d+/)[0];

                // Si es un radio button con valor, lo agregamos al contador
                if (target.type === 'radio' && target.checked) {
                    answeredQuestions.add(questionId);
                }

                // Si es un textarea y tiene texto, lo agregamos al contador
                if (target.tagName.toLowerCase() === 'textarea') {
                    if (target.value.trim() !== '') {
                        answeredQuestions.add(questionId);
                    } else {
                        answeredQuestions.delete(questionId);
                    }
                }

                // Actualizar el contador en la página
                counterElement.textContent = `${answeredQuestions.size} / ${totalQuestions} answered`;
            }
        });

        // También necesitamos escuchar el evento input en los textarea para verificar si el usuario borra el texto
        const textareas = document.querySelectorAll('textarea[name^="answers"]');
        textareas.forEach(textarea => {
            textarea.addEventListener('input', function() {
                const questionId = textarea.name.match(/\d+/)[0];

                if (textarea.value.trim() === '') {
                    answeredQuestions.delete(questionId);
                } else {
                    answeredQuestions.add(questionId);
                }

                // Actualizar el contador
                counterElement.textContent = `${answeredQuestions.size} / ${totalQuestions} answered`;
            });
        });

        // -------------------------
        // Validación antes de enviar el formulario
        // -------------------------
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            const totalTimeSeconds = (minutes * 60) + seconds;
            document.getElementById('total_time_seconds').value = totalTimeSeconds;

            // Verificamos si todos los inputs requeridos tienen valores
            const requiredInputs = form.querySelectorAll('input[required], textarea[required]');
            let allRequiredFilled = true;

            requiredInputs.forEach(input => {
                if (!input.value) {
                    allRequiredFilled = false;
                    input.classList.add('border-red-500');  // Resalta el campo vacío
                } else {
                    input.classList.remove('border-red-500');
                }
            });

            if (!allRequiredFilled) {
                Swal.fire({
                    title: 'Please complete all required fields.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure you want to submit?',
                text: "You won't be able to change your answers after submitting.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, submit!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Aquí enviamos el formulario manualmente
                    form.submit();
                }
            });
        });

    </script>
</x-app-layout>
