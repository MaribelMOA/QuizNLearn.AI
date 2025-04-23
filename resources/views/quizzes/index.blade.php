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
                    <button id="openModalButton" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-300 hover:border-primary transition-colors flex flex-col items-center justify-center p-10 text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Create New Quiz</h3>
                        <p class="text-gray-500">Create a new personalized quiz with AI</p>
                    </button>

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



    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex justify-center items-center hidden" >
        <div class="bg-white w-full max-w-3xl mx-auto sm:px-6 lg:px-8 p-6 rounded-lg shadow-lg relative">

            <!-- Header Section -->
            <div class="px-6 py-4 rounded-t-lg flex items-center justify-between bg-gradient-to-r from-indigo-600 to-blue-500 text-white">
                <!-- Título -->
                <h2 class="font-bold text-lg">
                    Create New Quiz
                </h2>

                <!-- Botón de cierre con ícono -->
                <button id="closeModalButton" class="text-white hover:text-gray-200 focus:outline-none">
                    <!-- Heroicon X -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>


            <div class="py-12 max-h-[80vh] overflow-y-auto">

                <form method="POST" action="{{ route('quizzes.store') }}" enctype="multipart/form-data" >
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <label for="title" class="block font-medium text-sm text-gray-700">Title</label>
                        <input id="title" name="title" type="text" maxlength="100" required autofocus class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>


                    {{-- Topic (if no source is provided) --}}
                    <div class="mb-4">
                        <label for="topic" class="block font-medium text-sm text-gray-700">Topic (if no source is used)</label>
                        <input id="topic" name="topic" type="text" maxlength="100" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('topic')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PDF File Upload -->
                    <div class="mb-4">
                        <label for="pdf_file" class="block font-medium text-sm text-gray-700">PDF File</label>
                        <input id="pdf_file" name="pdf_file" type="file" multiple accept="application/pdf" class="block mt-1 w-full text-sm text-gray-600" onchange="if(this.files.length > {{ $planLimits['pdf_files'] }}) { alert('Límite de PDFs alcanzado'); this.value=''; }">
                        @error('pdf_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- URLs -->
                    <div class="mb-4">
                        <label for="urlFieldsContainer" class="block font-medium text-sm text-gray-700">URLs (one per line)</label>
                        <div id="urlFieldsContainer" class="space-y-2">
                            <input type="url" name="urls[]" class="w-full p-2 border rounded" placeholder="Ingresa una URL">
                        </div>

                        <div class="flex items-center justify-between mt-3">
                            <button id="addUrlButton" type="button" class="inline-flex items-center px-4 py-2 bg-indigo-300 border border-transparent rounded-md font-semibold text-xs text-black-700  tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                ➕ Add URL
                            </button>
                            <span id="urlCount" class="text-sm text-gray-600 ml-4"></span>
                        </div>

                        <input type="hidden" id="maxUrls" value="{{ $planLimits['urls'] }}"> <!-- límite de URLs -->

                    </div>

                    {{-- Manual Text --}}
                    <div class="mb-4">
                        <label for="manual_text" class="block font-medium text-sm text-gray-700">Manual Text</label>
                        <textarea id="manual_text" name="manual_text" rows="4" maxlength="{{ $planLimits['text_limit'] }}"class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"></textarea>
                        @error('manual_text')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Question Count --}}
                    <div class="mb-4">
                        <label for="question_count" class="block font-medium text-sm text-gray-700">Number of Questions</label>
                        <input id="question_count" name="question_count" type="number" min="1" max="{{ $planLimits['max_questions'] }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('question_count')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Difficulty and Mode --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="difficulty" class="block font-medium text-sm text-gray-700">Difficulty</label>
                            <select id="difficulty" name="difficulty" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                            @error('difficulty')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mode" class="block font-medium text-sm text-gray-700">Mode</label>
                            <select id="mode" name="mode" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="Quiz">Quiz</option>
                                <option value="Study">Study</option>
                                <option value="Arena">Arena</option>
                            </select>
                            @error('mode')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Question Types --}}
                    <div class="mb-4">
                        <label class="block font-medium text-sm text-gray-700">Question Types</label>
                        <div class="flex flex-col space-y-2 mt-1">
                            <label><input type="checkbox" name="question_types[]" value="multiple_choice"> Multiple Choice</label>
                            <label><input type="checkbox" name="question_types[]" value="true_false"> True/False</label>
                            <label><input type="checkbox" name="question_types[]" value="open_ended" id="open_ended_checkbox"> Open-ended</label>
                            <p id="open_ended_warning" class="text-xs text-red-500 hidden">Open-ended questions are not allowed in Arena mode.</p>
                        </div>
                        @error('question_types')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('quizzes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Create Quiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
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

        <!-- Script para abrir y cerrar el modal --><!-- Script para abrir y cerrar el modal -->
        const availableCreations = @json($availableCreations);
       // const maxUrls = @json($planLimits['urls']);//planLimits.urls;

        document.addEventListener('DOMContentLoaded', () => {
            const openModalButton = document.getElementById('openModalButton');
            const closeModalButton = document.getElementById('closeModalButton');
            const modal = document.getElementById('modal');

            // Mostrar modal cuando se presiona el botón
            openModalButton.addEventListener('click', function (e) {
                e.preventDefault();

                if (availableCreations > 0) {
                    modal.classList.remove('hidden');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Límite alcanzado',
                        text: 'Ya no puedes crear más quizzes por ahora.',
                        confirmButtonColor: '#6366F1'
                    });
                }
            });

            // Mostrar modal si hay errores de validación
            @if ($errors->any())
            modal.classList.remove('hidden');
            @endif

            // Cerrar modal cuando se presiona el botón de cierre
            closeModalButton.addEventListener('click', () => {
                modal.classList.add('hidden');
            });

            // Cerrar modal si se hace clic fuera del contenido
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });

        /*ulrs*/
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('urlFieldsContainer');
            const addBtn = document.getElementById('addUrlButton');
            const countDisplay = document.getElementById('urlCount');
            const maxUrls = document.getElementById('maxUrls').value;
            console.log(maxUrls);
            const updateCount = () => {
                const currentCount = container.querySelectorAll('input[name="urls[]"]').length;
                countDisplay.textContent = `${currentCount} of ${maxUrls} URLs permited`;
                addBtn.disabled = currentCount >= maxUrls;
            };

            addBtn.addEventListener('click', () => {
                const currentCount = container.querySelectorAll('input[name="urls[]"]').length;
                if (currentCount < maxUrls) {
                    const newInput = document.createElement('div');
                    newInput.classList.add('flex', 'items-center', 'gap-2');
                    newInput.innerHTML = `
                    <input type="url" name="urls[]" class="w-full p-2 border rounded" placeholder="Ingresa otra URL">
                    <button type="button" class="text-red-500 hover:text-red-700 remove-url-btn">✖</button>
                `;
                    container.appendChild(newInput);
                    updateCount();
                } else {
                    alert('No puedes agregar más URLs. Has alcanzado el límite.');
                }
            });

            container.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-url-btn')) {
                    e.target.closest('div').remove();
                    updateCount();
                }
            });

            updateCount();
        });
        document.addEventListener('DOMContentLoaded', function () {
            // Validación de campos requeridos
            function checkFields() {
                const requiredFields = document.querySelectorAll('[required]');
                let allFilled = true;
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        allFilled = false;
                    }
                });
                document.getElementById('submit_button').disabled = !allFilled;
            }

            // Activar validación al cambiar inputs
            const inputs = document.querySelectorAll('[required]');
            inputs.forEach(input => {
                input.addEventListener('input', checkFields);
            });

            checkFields(); // Verifica al cargar

            // Agregar nuevas URLs
            const addUrlButton = document.getElementById('add_url');
            const urlContainer = document.getElementById('url_container');

            addUrlButton.addEventListener('click', function () {
                const newUrlInput = document.createElement('div');
                newUrlInput.className = 'flex gap-2 items-center mb-2';

                newUrlInput.innerHTML = `
            <input type="url" name="urls[]" required class="w-full p-2 border rounded" placeholder="Ingresa una URL" onchange="checkFields()">
            <button type="button" class="text-red-500 hover:text-red-700" onclick="this.parentElement.remove(); checkFields();">❌</button>
        `;

                urlContainer.appendChild(newUrlInput);
                checkFields();
            });

            // Validación modo Arena y preguntas abiertas
            const modeSelect = document.getElementById('mode');
            const openEndedCheckbox = document.getElementById('open_ended_checkbox');
            const openEndedWarning = document.getElementById('open_ended_warning');

            function validateQuestionTypes() {
                if (modeSelect.value === 'Arena' && openEndedCheckbox.checked) {
                    openEndedWarning.classList.remove('hidden');
                    openEndedCheckbox.checked = false;
                } else {
                    openEndedWarning.classList.add('hidden');
                }
            }

            modeSelect.addEventListener('change', validateQuestionTypes);
            openEndedCheckbox.addEventListener('change', validateQuestionTypes);
        });









        {{--document.addEventListener('DOMContentLoaded', function () {--}}
        {{--    @if ($errors->any())--}}
        {{--    document.getElementById('modal').classList.remove('hidden');--}}
        {{--    @endif--}}
        {{--});--}}




    </script>
</x-app-layout>


