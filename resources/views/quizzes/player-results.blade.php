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


                <!-- Polling script -->
{{--                <script>--}}
{{--                    const arenaId = {{ $arenaGame->id }};--}}
{{--                    const questionId = {{ $question->id }};--}}
{{--                    const score = {{ $score }};--}}

{{--                    console.log("Iniciando polling para check-status...");--}}
{{--                    console.log("arenaId:", arenaId, "questionId:", questionId, "score:", score);--}}

{{--                    const interval = setInterval(() => {--}}
{{--                        console.log("Enviando solicitud a:", `/arena/${arenaId}/question/${questionId}/check-status`);--}}

{{--                        fetch(`/arena/${arenaId}/question/${questionId}/check-status`)--}}
{{--                            .then(res => {--}}
{{--                                console.log("Respuesta recibida:", res);--}}
{{--                                return res.json();--}}
{{--                            })--}}
{{--                            .then(data => {--}}
{{--                                console.log("Datos JSON recibidos:", data);--}}

{{--                                if (data.ready) {--}}
{{--                                    console.log("Pregunta lista. Redirigiendo al resultado...");--}}
{{--                                    clearInterval(interval);--}}
{{--                                    window.location.href = `/arena/${arenaId}/question/${questionId}/result/${score}`;--}}
{{--                                } else {--}}
{{--                                    console.log("Aún no está lista la pregunta. Esperando...");--}}
{{--                                }--}}
{{--                            })--}}
{{--                            .catch(err => {--}}
{{--                                console.error("Error al hacer fetch:", err);--}}
{{--                            });--}}
{{--                    }, 5000);--}}
{{--                </script>--}}

            @else
                <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
                    <div class="p-6 text-center">


                        <div class="inline-flex items-center bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <span class="font-medium">This question's Score: {{ $score }}</span>
                        </div>

                    </div>
                </div>
                <!-- Ranking Table -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h2 class="text-lg font-semibold mb-4 text-gray-700">Ranking de Jugadores</h2>
                    <table class="w-full text-left table-auto">
                        <thead class="bg-gray-100 text-sm text-gray-600">
                        <tr>
                            <th class="py-2 px-3">#</th>
                            <th class="py-2 px-3">Name</th>
                            <th class="py-2 px-3">Total Score</th>
                        </tr>
                        </thead>
                        <tbody class="text-gray-800">
                        @foreach($ranking as $index => $player)
                            <tr class="{{ $index === 0 ? 'bg-yellow-100 font-bold' : '' }}">
                                <td class="py-2 px-3">{{ $index + 1 }}</td>
                                <td class="py-2 px-3">{{ $player['name'] }}</td>
                                <td class="py-2 px-3">{{ $player['total_score'] }}</td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

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

