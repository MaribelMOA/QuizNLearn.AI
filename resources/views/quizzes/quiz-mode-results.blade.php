<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-3xl text-gray-800">Quiz Results</h2>

            <div class="flex items-center space-x-2">
                <h2 class="text-sm text-gray-600">Score:</h2>
                <span class="text-lg font-bold text-indigo-600">{{ $score }}%</span>
            </div>
        </div>
    </x-slot>


    <div class="py-6">
        <div class="max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-2xl mt-6">

            <h3 class="text-2xl font-semibold text-gray-800 mb-6">Your Answers</h3>

            <div class="space-y-8">
                <ul class="space-y-4 mt-4">
                    @foreach ($quiz->quizQuestions as $question)
                        <li class="border-b border-gray-200 pb-4">

                            <div class="p-6 bg-gray-50 rounded-lg border border-gray-200">
                              <p class="font-semibold text-gray-800">{{ $loop->iteration }}. {{ $question->question_text }}</p>

                                @php
                                    $userAnswer = $answers[$question->id] ?? null;
                                    $correctAnswer = $question->quizQuestionAnswers->where('is_correct', true)->first();
                                    $isAIValid = $aiValidations[$question->id] ?? null;
                                @endphp
                                    <!-- Mostrar respuestas de opción múltiple -->
                                @if($question->type->name !== 'open_question')
                                    <div class="space-y-2">
                                        @foreach ($question->quizQuestionAnswers as $answer)
                                            <label class="flex items-center space-x-2">
                                                <input type="radio" disabled class="text-indigo-600"
                                                    {{ $userAnswer == $answer->id ? 'checked' : '' }}>
                                                <span class="text-gray-700">{{ $answer->answer_text }}@if ($answer->is_correct)
                                                        <span class="ml-2 text-green-600 font-semibold">(Correct)</span>
                                                    @endif</span>
                                            </label>
                                        @endforeach
                                    </div>

                                    <!-- Mostrar respuesta de pregunta abierta -->
                                @else
                                    <div class="space-y-2">
                                        <label for="userAnswer" class="text-gray-700">Your Answer:</label>
                                        <textarea disabled class="w-full p-2 border border-gray-300 rounded-md" rows="4">{{ $userAnswer }}</textarea>
                                    </div>
                                @endif

                                <div class="mt-4 space-y-2">
                                    <!-- Respuesta seleccionada y su estado -->
                                    <p class="text-gray-700 mt-2">
                                        Selected answer:
                                        @if($userAnswer == $correctAnswer->id || ($isAIValid && $question->type->name === 'open_question'))
                                            <span class="text-green-600">Correct</span>
                                        @else
                                            <span class="text-red-600">Incorrect</span>
                                        @endif
                                    </p>

                                    <div class="mt-3 p-4 bg-indigo-50 border-l-4 border-indigo-400 text-indigo-700 rounded-md">
                                        <p class="text-sm">Explanation:{{ $correctAnswer->explanation }}</p>
                                    </div>
                                </div>
                            </div>

                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="py-6">
                <h3 class="text-center text-2xl font-semibold text-gray-800">Overview</h3>

                <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 text-base">

                    <div class="p-4 bg-gray-100 rounded-md">
                        <p><span class="font-semibold">Total questions:</span> {{ $totalQuestions }}</p>
                        <p><span class="font-semibold">Correct answers:</span> {{ $correctAnswersCount }}</p>
                        <p><span class="font-semibold">Incorrect answers:</span> {{ $totalQuestions - $correctAnswersCount }}</p>
                    </div>
                    <div class="p-4 bg-gray-100 rounded-md">
                        <p><span class="font-semibold">Total time:</span> {{ gmdate('i:s', $totalTimeSeconds) }} minutes</p>
                        <p><span class="font-semibold">Final score:</span> {{ $score }}%</p>
                    </div>
                </div>

                @if(isset($xpGained) && $xpGained > 0)
                    <div class="mt-6 p-4 bg-green-100 text-green-800 rounded-md text-center">
                        🎉 Congratulations! You earned <span class="font-bold">{{ $xpGained }} XP</span> points!
                    </div>
                @else
                    <div class="mt-6 p-4 bg-yellow-100 text-yellow-800 rounded-md text-center">
                        🌟 Better luck next time! Keep going, you'll get there! 💪
                    </div>
                @endif



                <div class="mt-8 text-center">
                    <form action="{{ route('quizzes.index') }}" method="GET">
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-lg rounded-md font-semibold transition">
                            Finish Viewing Results
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>


</x-app-layout>


