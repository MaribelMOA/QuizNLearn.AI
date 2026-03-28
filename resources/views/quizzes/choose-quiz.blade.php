<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2">
            <!-- Botón con solo flecha y tooltip -->
            <a href="{{ route('quizzes.index') }}"
               class="p-2 rounded-md bg-gray-200 text-gray-600 hover:bg-gray-300 transition-colors"
               title="Volver a todos los cuestionarios">
                <!-- Ícono de flecha -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7" />
                </svg>
            </a>

            <!-- Título -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Choose an Arena Mode Quizz') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">


        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">




            <!-- Quiz Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Create New Quiz Card -->
                <button id="openModalButton" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-300 hover:border-primary transition-colors flex flex-col items-center justify-center p-10 text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Create New Arena Mode Quiz</h3>
                    <p class="text-gray-500">Create a new personalized quiz with AI specifically for Arena mode</p>
                </button>

                <!-- Quiz Cards -->
                @forelse($questionnaires as $questionnaire)
                    @include('quizzes._quiz-card-arena', ['questionnaire' => $questionnaire])
                    <script>

                        document.addEventListener("DOMContentLoaded", function() {
                            const startButton = document.getElementById('start-quiz-{{ $questionnaire->id }}');
                            const modeSelect = document.getElementById('play_mode_{{ $questionnaire->id }}'); // Asegúrate de usar IDs únicos si hay varios select
                            const hasOpenQuestions = {{ $questionnaire->hasOpenQuestions ? 'true' : 'false' }};
                            const arenaModeUses = {{ $arenaModeUses }};

                            startButton.addEventListener('click', function(event) {
                                event.preventDefault();

                                const selectedMode = modeSelect.value;

                                // --- Validaciones según el modo seleccionado ---

                                if (selectedMode === 'Arena') {
                                    // 1. Preguntas abiertas no permitidas en Arena
                                    if (hasOpenQuestions) {
                                        Swal.fire({
                                            title: 'Arena mode unavailable',
                                            text: 'This quiz contains open answer questions and is not allowed to be player in Arena Mode.',
                                            icon: 'error',
                                            confirmButtonText: 'Accept'
                                        });
                                        return;
                                    }
                                    // 2. Sin usos disponibles para Arena
                                    if (arenaModeUses <= 0) {
                                        Swal.fire({
                                            title: 'No uses left for Arena Mode',
                                            text: 'You have exhausted your uses for playing in Arena Mode. Upgrade your plan or choose another mode.',
                                            icon: 'warning',
                                            confirmButtonText: 'Accept'
                                        });
                                        return;
                                    }
                                } else if (selectedMode === 'Study') {
                                    // 3. Sin usos disponibles para Estudio
                                    if (studyModeUses <= 0) {
                                        Swal.fire({
                                            title: 'No uses left for Study Mode',
                                            text: 'You have exhausted your uses for playing in Study Mode. Upgrade your paln or choose another mode.',
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

                    </div>
                @endforelse
            </div>

        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $questionnaires->links() }}
        </div>
    </div>
    <!-- Modal Create -->
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

            <!-- Tabs -->
            <div class="mb-6">
                <div class="flex border-b">
                    <button id="tab-topic-btn" class="px-4 py-2 text-sm font-semibold text-gray-700 border-b-2 border-transparent hover:border-blue-500" data-tab="topic">Write topic</button>
                    <button id="tab-source-btn" class="ml-4 px-4 py-2 text-sm font-semibold text-gray-700 border-b-2 border-transparent hover:border-blue-500" data-tab="source">Upload Sources </button>
                </div>
            </div>


            <div class="py-12 max-h-[80vh] overflow-y-auto">

                <form id = "quizForm" method="POST" action="{{ route('quizzes.store') }}" enctype="multipart/form-data" >
                    @csrf

                    {{-- Title --}}
                    <div class="mb-4">
                        <label for="title" class="block font-medium text-sm text-gray-700">Title</label>
                        <input id="title" name="title" type="text" maxlength="100" required autofocus class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('title')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- TOPIC TAB CONTENT -->
                    <div id="tab-topic" class="tab-content mt-6">
                        {{-- Topic (if no source is provided) --}}
                        <div class="mb-4">
                            <label for="topic" class="block font-medium text-sm text-gray-700">Topic (if no source is used)</label>
                            <input id="topic" name="topic" type="text" maxlength="100" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            @error('topic')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- SOURCES TAB CONTENT -->
                    <div id="tab-source" class="tab-content mt-6 hidden">
                        <!-- PDF File Upload -->
                        <div class="mb-4">
                            <label for="pdf_file" class="block font-medium text-sm text-gray-700">PDF File</label>
                            <input id="pdf_file" name="pdf_file[]" type="file" multiple accept="application/pdf" class="block mt-1 w-full text-sm text-gray-600" onchange="if(this.files.length > {{ $planLimits['pdf_files'] }}) { alert('Límite de PDFs alcanzado'); this.value=''; }">
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
                    </div>



                    {{-- Question Count --}}
                    <div class="mb-4">
                        <label for="num_questions" class="block font-medium text-sm text-gray-700">Number of Questions</label>
                        <input required id="num_questions" name="num_questions" type="number" min="1" max="{{ $planLimits['max_questions'] }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        @error('num_questions')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Difficulty and Mode --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="difficulty_level" class="block font-medium text-sm text-gray-700">Difficulty</label>
                            <select required id="difficulty_level" name="difficulty_level" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="Easy">Easy</option>
                                <option value="Medium">Medium</option>
                                <option value="Hard">Hard</option>
                            </select>
                            @error('difficulty_level')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mode" class="block font-medium text-sm text-gray-700">Mode</label>
                            <select REQUIRED id="mode" name="mode" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">

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
                            <p id="open_ended_warning" class="text-xs text-red-500 hidden">Open-ended questions are not allowed in Arena mode.</p>
                        </div>
                        @error('question_types[]')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('quizzes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                            Cancel
                        </a>
                        <button type="submit" id="submit_button"  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Create Quiz
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Incluir SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//unpkg.com/alpinejs" defer></script>

    <script>


        <!-- Script para abrir y cerrar el modal --><!-- Script para abrir y cerrar el modal -->
        const availableCreations = @json($availableCreations);
        // const maxUrls = @json($planLimits['urls']);//planLimits.urls;

        document.addEventListener('DOMContentLoaded', () => {
            const openModalButton = document.getElementById('openModalButton');
            const closeModalButton = document.getElementById('closeModalButton');
            const modal = document.getElementById('modal');
            const quizForm = document.getElementById('quizForm');


            // Mostrar modal cuando se presiona el botón
            openModalButton.addEventListener('click', function (e) {
                e.preventDefault();

                if (availableCreations > 0) {
                    modal.classList.remove('hidden');
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Youve reached your limit',
                        text: 'You cant create more quizzes this month. Try upgrading plan or buying creations',
                        confirmButtonColor: '#6366F1'
                    });
                }
            });

            // Mostrar modal si hay errores de validación
            @if ($errors->any())
            modal.classList.remove('hidden');
            Swal.fire({
                icon: 'warning',
                title: 'Error with submission',
                text: @json($errors->all()),
                confirmButtonColor: '#6366F1'
            });
          //  console.log("Erros: ",@json($errors->all()));
            @endif

            // Cerrar modal cuando se presiona el botón de cierre
            closeModalButton.addEventListener('click', () => {
                modal.classList.add('hidden');
                resetQuizForm();
            });

            // Cerrar modal si se hace clic fuera del contenido
            window.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                    resetQuizForm();
                }
            });

            // Enviar formulario manualmente
            quizForm.addEventListener('submit', async function (e) {
                e.preventDefault(); // Detener envío automático

                const isValid = await validateQuizModal(); // Validar antes de enviar
                if (isValid) {
                    this.submit(); // Enviar manualmente solo si es válido
                    modal.classList.add('hidden'); // Cerrar el modal
                    resetQuizForm(); // Limpiar los campos del formulario
                }
            });
        });

        function resetQuizForm() {
            const form = document.getElementById('quizForm');
            form.reset();

            // También puedes limpiar elementos específicos si los generas dinámicamente:
            const urlInputs = document.querySelectorAll('input[name="urls[]"]');
            urlInputs.forEach(input => input.value = '');

            // Si usas checkboxes con selección múltiple, puedes desmarcarlos así:
            const checkboxes = document.querySelectorAll('input[name="question_types[]"]');
            checkboxes.forEach(cb => cb.checked = false);
        }


        /*ulrs*/
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('urlFieldsContainer');
            const addBtn = document.getElementById('addUrlButton');
            const countDisplay = document.getElementById('urlCount');
            const maxUrls = document.getElementById('maxUrls').value;
          //  console.log(maxUrls);
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
                    alert('You cant add more URLs. Youve reaches your limit.');
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



        });

        //TABS
        const tabBtns = {
            topic: document.getElementById('tab-topic-btn'),
            source: document.getElementById('tab-source-btn')
        };

        const tabContents = {
            topic: document.getElementById('tab-topic'),
            source: document.getElementById('tab-source')
        };

        function isTopicFilled() {
            return document.getElementById('topic').value.trim() !== '';
        }

        function isSourcesFilled() {
            const pdf = document.getElementById('pdf_file').files.length > 0;
            const urls = [...document.querySelectorAll('input[name="urls[]"]')].some(el => el.value.trim() !== '');
            const text = document.getElementById('manual_text').value.trim() !== '';
            return pdf || urls || text;
        }

        function clearTopicFields() {
            document.getElementById('topic').value = '';
        }

        function clearSourceFields() {
            document.getElementById('pdf_file').value = '';
            document.getElementById('manual_text').value = '';
            document.querySelectorAll('input[name="urls[]"]').forEach(el => el.value = '');
        }

        function switchTab(to) {
            for (let key in tabContents) {
                tabContents[key].classList.add('hidden');
                tabBtns[key].classList.remove('border-blue-500');
            }
            tabContents[to].classList.remove('hidden');
            tabBtns[to].classList.add('border-blue-500');
        }

        tabBtns.topic.addEventListener('click', () => {
            if (isSourcesFilled()) {
                Swal.fire({
                    title: '¿Change to "Write Quiz Topic"?',
                    text: 'The uploaded sources will be erased. Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, erase and change',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearSourceFields();
                        switchTab('topic');
                    }
                });
            } else {
                switchTab('topic');
            }
        });

        tabBtns.source.addEventListener('click', () => {
            if (isTopicFilled()) {
                Swal.fire({
                    title: '¿Change to "Write Quiz Topic"?',
                    text: 'The topic writen will be erased. Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#aaa',
                    confirmButtonText: 'Yes, erase and change',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        clearTopicFields();
                        switchTab('source');
                    }
                });
            } else {
                switchTab('source');
            }
        });





        const forbiddenDomains = ['facebook.com', 'instagram.com', 'tiktok.com'];


        async function validateQuizModal() {
            const defaultMode = document.getElementById('mode');
            const difficulty = document.getElementById('difficulty_level');
            const questionTypes = document.querySelectorAll('input[name="question_types[]"]:checked');

            // Validación de campos obligatorios
            if (!defaultMode.value || !difficulty.value || questionTypes.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required fields',
                    text: 'Please select Default Mode, Difficulty and at least one Question Type.',
                });
                return false;
            }



            // Validar URLs si se ingresaron
            const inputs = document.querySelectorAll('input[name="urls[]"]');
            const urls = Array.from(inputs).map(input => input.value.trim()).filter(val => val !== '');

            if (urls.length > 0) {
                try {
                    for (let url of urls) {
                        await validateUrlViaBackend(url);
                    }
                } catch (err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid URL',
                        text: err.message
                    });
                    return false;
                }
            }

            return true; // ✅ Todo bien
        }

        async function validateUrlViaBackend(url) {
            const response = await fetch('/validate-url', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ url })
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Unknown validation error');
            }

            return true;
        }


    </script>

</x-app-layout>
