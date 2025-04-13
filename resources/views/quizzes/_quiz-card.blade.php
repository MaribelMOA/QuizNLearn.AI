<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-100 hover:shadow-lg transition-shadow duration-300">
    <div class="p-4 border-b border-gray-100">
        <div class="flex justify-between items-start">
            <h3 class="text-lg font-semibold text-gray-800 truncate">{{ $questionnaire->title }}</h3>
            <div class="flex space-x-1">
                <button onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this quiz?')) document.getElementById('delete-quiz-{{ $questionnaire->id }}').submit();" class="text-gray-400 hover:text-red-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
                <form id="delete-quiz-{{ $questionnaire->id }}" action="{{ route('quizzes.destroy', $questionnaire->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
        <div class="mt-2">
            <span class="text-sm text-gray-500">{{ $questionnaire->quiz_questions_count ?? 0 }} questions</span>
        </div>
    </div>

    <div class="p-4 bg-gray-50">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center">
                @if($questionnaire->mode == 'quiz')
                    <svg class="w-5 h-5 text-primary mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span class="text-sm font-medium">Quiz Mode</span>
                @elseif($questionnaire->mode == 'study')
                    <svg class="w-5 h-5 text-secondary mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <span class="text-sm font-medium">Study Mode</span>
                @elseif($questionnaire->mode == 'arena')
                    <svg class="w-5 h-5 text-accent mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span class="text-sm font-medium">Arena Mode</span>
                @endif
            </div>

            <div class="px-2 py-1 rounded text-xs font-medium
                {{ $questionnaire->difficulty_level == 'easy' ? 'bg-green-100 text-green-800' : '' }}
                {{ $questionnaire->difficulty_level == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $questionnaire->difficulty_level == 'hard' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst($questionnaire->difficulty_level) }}
            </div>
        </div>

        <div class="flex justify-between mt-4">
            <a href="{{ route('quizzes.show', $questionnaire->id) }}" class="text-sm text-primary hover:text-blue-700 font-medium">
                View Details
            </a>

            <div class="flex space-x-2">
                @if($questionnaire->mode == 'study' || $questionnaire->mode == 'quiz')
                    <a href="{{ route('quizzes.play', ['questionnaire' => $questionnaire->id, 'mode' => 'study']) }}" class="inline-flex items-center px-3 py-1 bg-secondary text-white text-sm rounded hover:bg-green-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        Study
                    </a>
                @endif

                @if($questionnaire->mode == 'arena' || $questionnaire->mode == 'quiz')
                    <a href="{{ route('quizzes.play', ['questionnaire' => $questionnaire->id, 'mode' => 'arena']) }}" class="inline-flex items-center px-3 py-1 bg-accent text-white text-sm rounded hover:bg-yellow-600">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Arena
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
