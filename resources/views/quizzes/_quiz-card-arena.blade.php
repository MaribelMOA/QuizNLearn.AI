<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow h-62 flex flex-col">
    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            @php
                $difficultyColors = [
                    'Easy' => 'bg-emerald-100 text-emerald-800',
                    'Medium' => 'bg-amber-100 text-amber-800',
                    'Hard' => 'bg-rose-100 text-rose-800',
                ][$questionnaire->difficulty_level] ?? 'bg-gray-100 text-gray-800';

                $modeColors = [
                    'Quiz' => 'bg-indigo-100 text-indigo-800',
                    'Study' => 'bg-violet-100 text-violet-800',
                    'Arena' => 'bg-violet-100 text-blue-800',
                ][$questionnaire->mode] ?? 'bg-gray-100 text-gray-800';
            @endphp

            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $difficultyColors }}">
                {{ ucfirst($questionnaire->difficulty_level) }}
            </span>

            <div class="text-xs text-gray-500">
                {{ $questionnaire->created_at->format('M d, Y') }}
            </div>
        </div>



    </div>
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: '¡Éxito!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                });
            });
        </script>
    @endif



    <div class="p-4 flex-grow">

        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">{{ $questionnaire->title }}</h3>



        <div class="flex items-center text-gray-600 mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $questionnaire->num_questions}} {{ Str::plural('question', $questionnaire->num_questions) }}</span>
        </div>

        <!-- Mostrar los tipos de preguntas asociados al cuestionario -->
        @if ($questionnaire->questionTypes->isNotEmpty())
            <div class="mb-2">
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                @foreach ($questionnaire->questionTypes as $type)
                    {{ $type->name }}@if (!$loop->last), @endif
                @endforeach
            </span>
            </div>
        @endif

        <div class="flex items-center text-gray-600 mb-2">
            <p>Play Mode: </p>
            <div class="flex items-center space-x-2">
                <label for="play_mode_{{ $questionnaire->id }}" class="text-sm text-gray-600">            </label>
                <select name="mode" id="play_mode_{{ $questionnaire->id }}" class="mt-1 block w-40 text-gray-600 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Arena" {{ $questionnaire->mode == 'Arena' ? 'selected' : '' }}>Arena</option>
                </select>

                <!-- Mostrar mensaje si el modo no es válido -->
                <div id="error-message" class="alert alert-danger" style="display: none;">
                    No puedes usar el Modo Arena con preguntas abiertas. Elige otro modo o modifica las preguntas.
                </div>
            </div>
        </div>

    </div>

    <div class="p-2 border-t border-gray-200 mt-auto flex justify-center items-center">


        <div class="flex items-center  space-x-2">

            <a  id="start-quiz-{{ $questionnaire->id }}"  href="" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Start
            </a>
        </div>
    </div>
</div>
