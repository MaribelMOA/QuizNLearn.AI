<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Quizzes') }}
        </h2>
    </x-slot>

    <div class="py-6 ">

        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 flex items-center justify-center p-4" onclick="redirectToQuizzes(event)">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center border-b p-4">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">📋</span>
                        <h3 class="text-2xl font-bold text-gray-800">Quiz Info</h3>
                    </div>
                    <!-- Button to close and redirect -->
                    <a href="{{ route('quizzes.index') }}" class="text-red-500 hover:text-red-700"> <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                                                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>

                <div class="p-6">


                    <div class="mb-8">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center mb-4">
                                <span class="text-xl mr-2">📌</span>
                                <h4 class="text-xl font-semibold text-gray-700">Quiz Overview</h4>
                            </div>
                            <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"  class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md flex items-center transition-colors" onclick="return confirm('Are you sure you want to delete this quiz?')">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                    Delete
                                </button>

                            </form>

                        </div>

                        <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $quiz->title }}</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div>
                                <div class="flex items-center mb-3">
                                    <span class="text-gray-500 mr-2">📅</span>
                                    <span class="text-gray-600">Created: </span>
                                    <span class="ml-1 font-medium">{{ $quiz->created_at->format('M d, Y')  }}</span>
                                </div>

                                <div class="flex items-center mb-3">
                                    <span class="text-orange-500 mr-2">🔥</span>
                                    <span class="text-gray-600">Level: </span>
                                    <span class="ml-1 font-medium">
                                        {{ ucfirst($quiz->difficulty_level) }}
                                    </span>
                                </div>

                                <div class="flex items-center mb-3">
                                    <span class="text-red-500 mr-2">🎯</span>
                                    <span class="text-gray-600">Default Mode: </span>
                                    <span class="ml-1 font-medium">
                                        {{ ucfirst($quiz->mode) }} Mode
                                    </span>
                                </div>

                                <div class="flex items-center mb-3">
                                    <span class="text-purple-500 mr-2">❓</span>
                                    <span class="text-gray-600">Number of Questions: </span>
                                    <span class="ml-1 font-medium">{{ $quiz->num_questions }}</span>
                                </div>
                            </div>

                            <div>


                                @if ($quiz->questionTypes->isNotEmpty())
                                    <div class="flex items-center mb-3">
                                        <span class="text-blue-500 mr-2">🔠</span>
                                        <span class="text-gray-600">Question Types: </span>

                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            @foreach ($quiz->questionTypes as $type)
                                                {{ ucwords(str_replace('_', ' ', $type->name)) }}@if (!$loop->last), @endif
                                            @endforeach
                                         </span>
                                    </div>
                                @endif


                                    @if ($timesUsed > 0)
                                        <div class="flex items-center mb-3">
                                            <span class="text-gray-500 mr-2">🔄</span>
                                            <span class="text-gray-600">Times Used: </span>
                                            <span class="ml-1 font-medium">{{ $timesUsed }}</span>
                                        </div>

                                        <div class="flex items-center mb-3">
                                            <span class="text-gray-500 mr-2">📅</span>
                                            <span class="text-gray-600">Last Played: </span>
                                            <span class="ml-1 font-medium">{{ $lastPlayed }}</span>
                                        </div>

                                        <div class="flex items-center mb-3">
                                            <span class="text-blue-500 mr-2">📖</span>
                                            <span class="text-gray-600">Last Mode Played: </span>
                                            <span class="ml-1 font-medium">{{ $lastModePlayed }}</span>
                                        </div>
                                    @else
                                        <div class="flex items-center mb-3">
                                            <span class="text-gray-500 mr-2">🔄</span>
                                            <span class="text-gray-600">Times Used: </span>
                                            <span class="ml-1 font-medium">0</span>
                                        </div>

                                        <div class="flex items-center mb-3">
                                            <span class="text-gray-500 mr-2">📅</span>
                                            <span class="text-gray-600">Last Played: </span>
                                            <span class="ml-1 font-medium">Haven't played yet</span>
                                        </div>

                                        <div class="flex items-center mb-3">
                                            <span class="text-gray-500 mr-2">📖</span>
                                            <span class="text-gray-600">Last Mode Played: </span>
                                            <span class="ml-1 font-medium">Haven't played yet</span>
                                        </div>
                                    @endif

                            </div>
                        </div>

                    </div>

                    @if ($showPerformanceStats)
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <span class="text-xl mr-2">📊</span>
                            <h4 class="text-xl font-semibold text-gray-700">Performance Statistics</h4>
                        </div>

                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-center mb-2">
                                    <span class="text-xl">📈</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-1">Avg. Score</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $quiz->avgScore }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-center mb-2">
                                    <span class="text-xl">🏆</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-1">Best Score</p>
                                <p class="text-3xl font-bold text-yellow-600">{{ $quiz->bestScore }}</p>
                            </div>

                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-center mb-2">
                                    <span class="text-xl">📉</span>
                                </div>
                                <p class="text-gray-600 text-sm mb-1">Worst Score</p>
                                <p class="text-3xl font-bold text-gray-800">{{ $quiz->worstScore }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <span class="text-xl mr-2">🎮</span>
                            <h4 class="text-xl font-semibold text-gray-700">Play Quiz</h4>
                        </div>

                        <p class="text-gray-600 mb-4">Choose a mode to play this quiz:</p>

                        @php
                            $hasOpenQuestions = $quiz->questionTypes->pluck('name')->contains('open_question');
                        @endphp

                        <div class="flex justify-center gap-6">
                            <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-md flex items-center transition-colors">
                                <span class="mr-2">📝</span>
                                Quiz Mode
                            </button>

                            <!-- Study Mode -->
                            <button
                                class="px-6 py-3 rounded-md flex items-center transition-colors
                {{ $studyModeUses == 0 ? 'bg-gray-400 cursor-not-allowed text-gray-400 ' : 'bg-pink-500 hover:bg-pink-600 text-white' }}"
                                {{ $studyModeUses == 0 ? 'disabled' : '' }}>
                                <span class="mr-2">📖</span>
                                Study Mode
                            </button>

                            <!-- Arena Mode -->
                            <button
                                class="px-6 py-3 rounded-md flex items-center transition-colors
                {{ ($arenaModeUses == 0 || $hasOpenQuestions)  ? 'bg-gray-200 cursor-not-allowed text-gray-400 ' : 'bg-blue-500 hover:bg-blue-600 text-white' }}"
                                {{ ($arenaModeUses == 0 || $hasOpenQuestions)  ? 'disabled' : '' }}>
                                <span class="mr-2">🎯</span>
                                Arena Mode
                            </button>
                        </div>

                        <!-- Mensajes informativos -->
                        <div class="mt-4 text-center">
                            @if ($studyModeUses == 0)
                                <p class="text-red-500">You have no Study Mode uses remaining.</p>
                            @endif

                            @if ($arenaModeUses == 0)
                                <p class="text-red-500">You have no Arena Mode uses remaining.</p>
                            @endif

                            @if ($hasOpenQuestions)
                                <p class="text-red-500">Arena Mode is not available for quizzes with open-ended questions.</p>
                            @endif
                        </div>
                    </div>



                    <div>
                        <div class="flex items-center mb-4">
                            <span class="text-xl mr-2">⬇️</span>
                            <h4 class="text-xl font-semibold text-gray-700">Download Quiz</h4>
                        </div>

                        <p class="text-gray-600 mb-4">Export your quiz in different formats:</p>

                        <!-- Contenedor para los botones con flexbox centrado y márgenes -->
                        <div class="flex justify-center gap-6">
                            <!-- Botón PDF con icono -->
                            <a href="{{ route('quiz.downloadPdf', ['quiz_id' => $quiz->id]) }}"
                               class="bg-blue-600 hover:bg-blue-700 text-white w-full max-w-xs px-8 py-4 rounded-md flex items-center justify-center transition-colors bg-red-500 border border-red-500">
                                <!-- Icono PDF -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7v10M13 7l4 4m-4-4l-4 4" />
                                </svg>
                                Download PDF
                            </a>


                            <!-- Botón JSON con icono -->
                            <button
                                onClick="handleDownloadJSON({{$quiz->quiz_data}})"
                                class="bg-green-600 hover:bg-green-700 text-white w-full max-w-xs px-8 py-4 rounded-md flex items-center justify-center transition-colors"
                            >
                                <!-- Icono JSON -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7v10M16 7l4 4m-4-4l-4 4" />
                                </svg>
                                Download JSON
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Función para redirigir al usuario a quizzes.index si hace clic fuera del modal
        function redirectToQuizzes(event) {
            // Verifica si el clic fue fuera del modal
            if (event.target === event.currentTarget) {
                window.location.href = "{{ route('quizzes.index') }}";
                // Redirige a quizzes.index
            }
        }

        function handleDownloadJSON(quiz) {
            // Convertir el objeto JSON a una cadena
            const jsonString = JSON.stringify(quiz, null, 2);

            // Crear un Blob con el contenido JSON
            const blob = new Blob([jsonString], { type: 'application/json' });

            // Crear una URL para el Blob
            const url = URL.createObjectURL(blob);

            // Crear un enlace de descarga
            const a = document.createElement('a');
            a.href = url;
            a.download = `myquiz.json`; // El nombre del archivo descargado (quiz name y id)

            // Programar el clic en el enlace para descargar el archivo
            document.body.appendChild(a);
            a.click();

            // Limpiar el enlace después de usarlo
            document.body.removeChild(a);

            // Liberar la URL del Blob
            URL.revokeObjectURL(url);
        }

    </script>
</x-app-layout>

