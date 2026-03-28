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
                                <p class="text-purple-200">Results</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Question Results Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Question Card -->
                <div class="lg:col-span-3 bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-indigo-500 px-6 py-4">
                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-bold text-white">Question {{ $questionNumber }} of {{ $totalQuestions }}</h2>
                            <span class="bg-white bg-opacity-20 px-3 py-1 rounded-lg text-white text-sm">
                                {{ $quiz->title }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="bg-purple-50 border border-purple-100 rounded-lg p-4">
                            <h3 class="text-xl font-semibold text-gray-800">{{ $question->question_text }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Answer Distribution and Ranking -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Answer Distribution -->
                <div class="lg:col-span-2 bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Answer Distribution</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        @php
                            $totalVotes = $answerCounts->sum();

                            $colors = ['bg-red-500', 'bg-green-500', 'bg-yellow-500', 'bg-blue-500'];
                        @endphp

                        @foreach($question->quizQuestionAnswers as $index => $answer)
                            @php
                                $votes = $answerCounts[$answer->id] ?? 0;
                                $percentage = $totalVotes > 0 ? round(($votes / $totalVotes) * 100) : 0;
                                $color = $colors[$index % count($colors)];
                                $borderColor = $answer->is_correct ? 'border-green-500' : 'border-gray-200';
                                $bgColor = $answer->is_correct ? 'bg-green-50' : 'bg-white';
                            @endphp

                            <div class="border-2 {{ $borderColor }} rounded-lg p-4 {{ $bgColor }} transition-colors">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full {{ $color }} text-white flex items-center justify-center font-bold mr-3">
                                            {{ chr(65 + $index) }}
                                        </div>
                                        <span class="text-gray-800 font-medium">{{ $answer->answer_text }}</span>

                                        @if($answer->is_correct)
                                            <span class="ml-3 bg-green-100 text-green-800 text-xs font-semibold px-2.5 py-0.5 rounded-full flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Correct
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-gray-600 text-sm">{{ $votes }} votes</span>
                                        <span class="ml-2 text-gray-800 font-bold">{{ $percentage }}%</span>
                                    </div>
                                </div>

                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="{{ $color }} h-2.5 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Player Ranking -->
                <div class="lg:col-span-1 bg-white rounded-xl shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-6 py-4">
                        <h2 class="text-xl font-bold text-white">Player Ranking</h2>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            @foreach($ranking as $index => $player)
                                @if($isLastQuestion && $index >= 3)
                                    @continue
                                @endif

                                @php
                                    $rankClass = '';
                                    $medalIcon = '';

                                    if($isLastQuestion && $index === 0) {
                                        // Primer lugar en la última pregunta – diseño especial
                                        $rankClass = 'bg-yellow-100 border-yellow-300 shadow-lg';
                                        $medalIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2l2.39 7.26h7.61l-6.16 4.48L17.88 22 12 17.77 6.12 22l1.04-8.26L1 9.26h7.61z"/>
                                                      </svg>';
                                    }
                                @endphp

                                <div class="border rounded-lg p-3 flex items-center justify-between {{ $rankClass }}">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3 font-bold text-indigo-800">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">{{ $player->name }}</span>
                                            @if(!empty($medalIcon))
                                                <span class="ml-2">{!! $medalIcon !!}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-indigo-600">{{ $player->score }}</span>
                                        <span class="text-gray-600">pts</span>

                                        @if(isset($player->score_change) && $player->score_change > 0)
                                            <span class="ml-1 text-green-600 text-sm">+{{ $player->score_change }}</span>
                                        @elseif(isset($player->score_change) && $player->score_change == 0)
                                            <span class="ml-1 text-gray-500 text-sm">+0</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-6 flex justify-center">
                @if (!$isLastQuestion)
                    <form action="{{ route('arena.next-question', ['arenaGame' => $arenaGameId, 'question' => $nextQuestion->id]) }}" method="POST" class="w-full max-w-md">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 flex items-center justify-center">
                            <span>Next Question</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>

                    </form>
                @else
                    <form action="{{ route('arena.finish_game', ['arenaGameId' => $arenaGameId]) }}" method="POST" class="w-full max-w-md">
                        @csrf
                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 flex items-center justify-center">
                            <span>Finish Quiz</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

</x-app-layout>

