<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 py-10">
        <div class="max-w-3xl mx-auto px-4">
            <!-- Game Header -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h1 class="text-xl font-bold text-white">Resultados del Quiz</h1>
                    <span class="text-white font-medium">{{ $quiz?->title ?? '...' }}</span>
                </div>
            </div>
            @if($waiting || !($ranking && count($ranking) > 0) )
                <!-- Waiting Message -->
                <div class="bg-white rounded-xl shadow-md p-6 text-center">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Waiting for everyone to answer or for time to finish</h2>
                    <p class="text-gray-600 mb-2">The results will be available shortly.</p>
                    <div class="flex justify-center mt-4">
                        <svg class="animate-spin h-6 w-6 text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                    </div>
                </div>

            @else
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="p-6 text-center">


                        <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span class="font-medium">This question's Score: {{ $score }}</span>
                        </div>
                        <!-- Mostrar si fue correcta o incorrecta -->
                        <div class="mt-2">
                            @if($score > 0)
                                <p class="text-green-600 font-semibold">Correct! 🎉</p>
                            @else
                                <p class="text-red-500 font-semibold">Incorrect 😕</p>
                            @endif
                        </div>

                    </div>
                </div>

                <!-- Question Results Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <!-- Question Card -->
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-lg overflow-hidden">
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
                                    @php
                                        $rankClass = '';
                                        $medalIcon = '';

                                        if($index === 0) {
                                            $rankClass = 'bg-amber-50 border-amber-200';
                                            $medalIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>';
                                        } else if($index === 1) {
                                            $rankClass = 'bg-gray-50 border-gray-200';
                                            $medalIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                        </svg>';
                                        } else if($index === 2) {
                                            $rankClass = 'bg-amber-50 border-amber-200 opacity-70';
                                            $medalIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
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

                {{--                <!-- Ranking Table -->--}}
                {{--                <div class="bg-white rounded-xl shadow-md p-6">--}}
                {{--                    <h2 class="text-lg font-semibold mb-4 text-gray-700">Ranking de Jugadores</h2>--}}
                {{--                    <table class="w-full text-left table-auto">--}}
                {{--                        <thead class="bg-gray-100 text-sm text-gray-600">--}}
                {{--                        <tr>--}}
                {{--                            <th class="py-2 px-3">#</th>--}}
                {{--                            <th class="py-2 px-3">Name</th>--}}
                {{--                            <th class="py-2 px-3">Total Score</th>--}}
                {{--                        </tr>--}}
                {{--                        </thead>--}}
                {{--                        <tbody class="text-gray-800">--}}
                {{--                        @foreach($ranking as $index => $player)--}}
                {{--                            <tr class="{{ $index === 0 ? 'bg-yellow-100 font-bold' : '' }}">--}}
                {{--                                <td class="py-2 px-3">{{ $index + 1 }}</td>--}}
                {{--                                <td class="py-2 px-3">{{ $player['name'] }}</td>--}}
                {{--                                <td class="py-2 px-3">{{ $player['total_score'] }}</td>--}}

                {{--                            </tr>--}}
                {{--                        @endforeach--}}
                {{--                        </tbody>--}}
                {{--                    </table>--}}
                {{--                </div>--}}

            @endif
        </div>
    </div>
    <script>
        const arenaId = {{ $arenaGame->id }};
        const questionId = {{ $question->id }};
        const score = {{ $score }};

        const interval = setInterval(() => {
            fetch(`/arena/${arenaId}/question/${questionId}/check-status`)
                .then(res => res.json())
                .then(data => {
                    console.log("Polling result:", data);

                    if (data.status === 'show_result') {
                        clearInterval(interval);
                        window.location.href = `/arena/${arenaId}/question/${questionId}/result/${score}`;
                    }

                    if (data.status === 'next_question') {
                        clearInterval(interval);
                        window.location.href = `/arena/play/${arenaId}`;
                    }

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

                    // status === 'waiting' → no hacemos nada
                });
        }, 3000);
    </script>

</x-app-layout>

