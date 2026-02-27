<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Nuevo Cuestionario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Mensaje si el usuario alcanzó el límite --}}
                    @if (session('limit_reached'))
                        <div class="mb-4 p-4 bg-yellow-100 text-yellow-800 rounded">
                            {{ session('limit_reached') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('quizzes.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- Título --}}
                        <div class="mb-4">
                            <label for="title" class="block font-medium text-sm text-gray-700">Título</label>
                            <input id="title" name="title" type="text" value="{{ old('title') }}" maxlength="100" required autofocus class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            @error('title')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción --}}
                        <div class="mb-4">
                            <label for="description" class="block font-medium text-sm text-gray-700">Descripción</label>
                            <textarea id="description" name="description" rows="3" maxlength="255" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('description') }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Categoría --}}
                        <div class="mb-4">
                            <label for="category" class="block font-medium text-sm text-gray-700">Categoría</label>
                            <input id="category" name="category" type="text" value="{{ old('category') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            @error('category')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tema (si no se ingresó ninguna fuente) --}}
                        <div class="mb-4">
                            <label for="topic" class="block font-medium text-sm text-gray-700">Tema (si no se usa fuente)</label>
                            <input id="topic" name="topic" type="text" value="{{ old('topic') }}" maxlength="100" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            @error('topic')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- PDF --}}
                        <div class="mb-4">
                            <label for="pdf_file" class="block font-medium text-sm text-gray-700">Archivo PDF</label>
                            <input id="pdf_file" name="pdf_file" type="file" accept="application/pdf" class="block mt-1 w-full text-sm text-gray-600">
                            @error('pdf_file')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- URLs --}}
                        <div class="mb-4">
                            <label for="source_urls" class="block font-medium text-sm text-gray-700">URLs (una por línea)</label>
                            <textarea id="source_urls" name="source_urls" rows="2" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('source_urls') }}</textarea>
                            @error('source_urls')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Texto Manual --}}
                        <div class="mb-4">
                            <label for="manual_text" class="block font-medium text-sm text-gray-700">Texto Manual</label>
                            <textarea id="manual_text" name="manual_text" rows="4" maxlength="1000" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">{{ old('manual_text') }}</textarea>
                            @error('manual_text')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Cantidad de preguntas --}}
                        <div class="mb-4">
                            <label for="question_count" class="block font-medium text-sm text-gray-700">Cantidad de preguntas</label>
                            <input id="question_count" name="question_count" type="number" min="1" max="50" value="{{ old('question_count') }}" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            @error('question_count')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Dificultad y Modo --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="difficulty" class="block font-medium text-sm text-gray-700">Dificultad</label>
                                <select id="difficulty" name="difficulty" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>Fácil</option>
                                    <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>Medio</option>
                                    <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>Difícil</option>
                                </select>
                                @error('difficulty')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="mode" class="block font-medium text-sm text-gray-700">Modo</label>
                                <select id="mode" name="mode" class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                    <option value="quiz" {{ old('mode') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                    <option value="study" {{ old('mode') == 'study' ? 'selected' : '' }}>Estudio</option>
                                    <option value="arena" {{ old('mode') == 'arena' ? 'selected' : '' }}>Arena</option>
                                </select>
                                @error('mode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tipos de preguntas --}}
                        <div class="mb-4">
                            <label class="block font-medium text-sm text-gray-700">Tipos de preguntas</label>
                            <div class="flex flex-col space-y-2 mt-1">
                                <label><input type="checkbox" name="question_types[]" value="multiple_choice" {{ is_array(old('question_types')) && in_array('multiple_choice', old('question_types')) ? 'checked' : '' }}> Opción Múltiple</label>
                                <label><input type="checkbox" name="question_types[]" value="true_false" {{ is_array(old('question_types')) && in_array('true_false', old('question_types')) ? 'checked' : '' }}> Verdadero/Falso</label>
{{--                                <label><input type="checkbox" name="question_types[]" value="open_question" {{ is_array(old('question_types')) && in_array('open_question', old('question_types')) ? 'checked' : '' }} id="open_ended_checkbox"> Respuesta Abierta</label>--}}
                                <p id="open_ended_warning" class="text-xs text-red-500 hidden">No se permiten preguntas abiertas en el modo Arena.</p>
                            </div>
                            @error('question_types')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('quizzes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-300 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                Cancelar
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Crear Cuestionario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Script para advertencia al seleccionar preguntas abiertas en modo Arena --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modeSelect = document.getElementById('mode');
            const openEndedCheckbox = document.getElementById('open_ended_checkbox');
            const warning = document.getElementById('open_ended_warning');

            function checkRestrictions() {
                if (modeSelect.value === 'arena' && openEndedCheckbox.checked) {
                    warning.classList.remove('hidden');
                    openEndedCheckbox.checked = false;
                } else {
                    warning.classList.add('hidden');
                }
            }

            modeSelect.addEventListener('change', checkRestrictions);
            openEndedCheckbox.addEventListener('change', checkRestrictions);
        });

        document.addEventListener('DOMContentLoaded', function () {
            const topic = document.getElementById('topic');
            const pdf = document.getElementById('pdf_file');
            const sourceUrls = document.getElementById('source_urls');
            const manualText = document.getElementById('manual_text');
            const submitBtn = document.querySelector('button[type="submit"]');

            function checkInputs() {
                const hasContent =
                    topic.value.trim() ||
                    pdf.files.length > 0 ||
                    sourceUrls.value.trim() ||
                    manualText.value.trim();

                submitBtn.disabled = !hasContent;
            }

            topic.addEventListener('input', checkInputs);
            pdf.addEventListener('change', checkInputs);
            sourceUrls.addEventListener('input', checkInputs);
            manualText.addEventListener('input', checkInputs);

            checkInputs(); // estado inicial
        });
    </script>
</x-app-layout>

