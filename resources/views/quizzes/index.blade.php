<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Quizzes') }}
        </h2>
    </x-slot>

    <div class="py-6">


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- 🟩 Grid de 2 tarjetas (Quiz Stats + Available Uses) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

                <!-- Quiz Stats Card -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 w-full">
                    <h3 class="text-base font-semibold text-green-800 mb-3">Quiz Stats</h3>
                    <div class="grid grid-cols-2 gap-6 text-lg">
                        <div class="flex items-center">
                            <span class="text-gray-600 mr-2">Total Quizzes</span>
                            <span class="font-bold text-gray-900 text-3xl">{{ $totalQuizzes }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-600 mr-2">Available Creations</span>
                            <span class="font-bold text-green-700 text-3xl">{{ $availableCreations }}</span>
                        </div>
                    </div>
                </div>

                <!-- Available Uses Card -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 w-full">
                    <h3 class="text-base font-semibold text-purple-800 mb-3">Available Uses</h3>
                    <div class="grid grid-cols-2 gap-6 text-lg">
                        <div class="flex items-center">
                            <span class="text-purple-700 mr-2">Study Mode</span>
                            <span class="text-sm bg-purple-100 text-purple-800 px-2 py-0.5 rounded-full w-fit">{{ $studyModeUses }} left</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-blue-700 mr-2">Arena Mode</span>
                            <span class="text-sm bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full w-fit">{{ $arenaModeUses }} left</span>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Search Bar -->
            <!-- <div class="mb-6">
                <form action="{{ route('quizzes.index') }}" method="GET">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" id="search-input" value="{{ request('search') }}" class="pl-10 pr-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50" placeholder="Search by title, mode, difficulty or exact number of questions">
                        @if(request('search'))
                            <button type="button" id="clear-search" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </form>
            </div>-->



            <!-- Search and Filter Section -->
            <!-- Search and Filter Section (Combinada) -->
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form id="search-form" action="{{ route('quizzes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-grow">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" id="search-input" value="{{ request('search') }}"
                                       class="pl-10 pr-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                       placeholder="Search by title, mode, difficulty or number of quesitons ">

                                @if(request('search'))
                                    <button type="button" id="clear-search" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                            onclick="document.getElementById('search-input').value=''; this.form.submit();">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <select name="difficulty" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="">All levels</option>
                                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Easy</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Hard</option>
                            </select>

                            <select name="mode" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="">All modes</option>
                                <option value="Quiz" {{ request('mode') == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="Study" {{ request('mode') == 'Study' ? 'selected' : '' }}>Study</option>
                                <option value="Arena" {{ request('mode') == 'Arena' ? 'selected' : '' }}>Arena</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Quiz Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Create New Quiz Card -->
                    <a href="{{ route('quizzes.create') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-300 hover:border-primary transition-colors flex flex-col items-center justify-center p-10 text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Create New Quiz</h3>
                        <p class="text-gray-500">Create a new personalized quiz with AI</p>
                    </a>

                    <!-- Quiz Cards -->
                    @forelse($questionnaires as $questionnaire)
                        @include('quizzes._quiz-card', ['questionnaire' => $questionnaire])
                    <script>

                        document.addEventListener("DOMContentLoaded", function() {
                            const startButton = document.getElementById('start-quiz-{{ $questionnaire->id }}');
                            const modeSelect = document.getElementById('play_mode_{{ $questionnaire->id }}'); // Asegúrate de usar IDs únicos si hay varios select
                            const hasOpenQuestions = {{ $questionnaire->hasOpenQuestions ? 'true' : 'false' }};
                            const arenaModeUses = {{ $arenaModeUses }};
                            const studyModeUses = {{ $studyModeUses }};

                            //LO DE DESHABILITAR
                         //   modeSelect.addEventListener('change', updateStartButtonStyle);


                            startButton.addEventListener('click', function(event) {
                                event.preventDefault();

                                const selectedMode = modeSelect.value;

                                // --- Validaciones según el modo seleccionado ---

                                if (selectedMode === 'Arena') {
                                    // 1. Preguntas abiertas no permitidas en Arena
                                    if (hasOpenQuestions) {
                                        Swal.fire({
                                            title: 'Modo Arena no disponible',
                                            text: 'Este cuestionario contiene preguntas abiertas y no se puede jugar en modo Arena.',
                                            icon: 'error',
                                            confirmButtonText: 'Aceptar'
                                        });
                                        return;
                                    }
                                    // 2. Sin usos disponibles para Arena
                                    if (arenaModeUses <= 0) {
                                        Swal.fire({
                                            title: 'Sin usos para Modo Arena',
                                            text: 'Has agotado tus usos para jugar en modo Arena. Vuelve más tarde o selecciona otro modo.',
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar'
                                        });
                                        return;
                                    }
                                } else if (selectedMode === 'Study') {
                                    // 3. Sin usos disponibles para Estudio
                                    if (studyModeUses <= 0) {
                                        Swal.fire({
                                            title: 'Sin usos para Modo Estudio',
                                            text: 'Has agotado tus usos para jugar en modo Estudio. Intenta más tarde o elige otro modo.',
                                            icon: 'warning',
                                            confirmButtonText: 'Aceptar'
                                        });
                                        return;
                                    }
                                }

                                // 4. Si pasa las validaciones: redirigir
                                window.location.href = `/quizzes/play/{{ $questionnaire->id }}?mode=${selectedMode}`;
                            });

                            // --- Mostrar tooltip si el botón está deshabilitado (opcional extra UX) ---

                            function updateStartButtonStyle() {
                                const selectedMode = modeSelect.value;

                                let shouldDisableVisually = false;

                                if (selectedMode === 'Arena') {
                                    if (hasOpenQuestions || arenaModeUses <= 0) {
                                        shouldDisableVisually = true;
                                    }
                                } else if (selectedMode === 'Study') {
                                    if (studyModeUses <= 0) {
                                        shouldDisableVisually = true;
                                    }
                                }

                                if (shouldDisableVisually) {
                                    startButton.classList.add('opacity-50', 'cursor-not-allowed');
                                } else {
                                    startButton.classList.remove('opacity-50', 'cursor-not-allowed');
                                }
                            }

                        });
                    </script>
                    @empty
                        <div class="col-span-full bg-white rounded-lg shadow-sm p-6 text-center border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No quizzes found</h3>
                            <p class="text-gray-500 mb-4">You haven't created any quizzes yet or none match your search criteria.</p>
                            <a href="{{ route('quizzes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                Create Your First Quiz
                            </a>
                        </div>
                    @endforelse
                </div>

            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $questionnaires->links() }}
            </div>
        </div>
    </div>
    <!-- Arena Mode Sidebar -->
    <div class="fixed right-0 top-0 h-full w-80 bg-white shadow-lg transform transition-transform duration-300 ease-in-out"
         x-data="{ open: false }"
         :class="{'translate-x-0': open, 'translate-x-full': !open}">

        <!-- Toggle Button -->
        <button @click="open = !open" class="absolute left-0 top-1/2 -translate-x-full bg-primary text-white p-3 rounded-l-lg shadow-md">
            <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
            </svg>
            <svg x-show="open" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>

        <!-- Arena Mode Content -->
        @include('quizzes._arena-mode')
    </div>
    <!-- Incluir SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Clear search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const clearButton = document.getElementById('clear-search');
            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    document.getElementById('search-input').value = '';
                });
            }

        });





    </script>
</x-app-layout>


