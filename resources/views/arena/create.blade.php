<div class="bg-gradient-to-r from-indigo-600 to-blue-500 p-6 rounded-lg">
    <h2 class="text-white text-lg font-bold mb-4">Select or Create Quiz for Arena Mode</h2>

    <div class="mb-6">
        <h3 class="text-white">Available Quizzes</h3>
        @foreach($quizzes as $quiz)
            <div class="flex items-center mb-3">
                <p class="text-white mr-3">{{ $quiz->title }}</p>
                <a href="{{ route('arena.start', ['quiz_id' => $quiz->id]) }}" class="text-blue-300">Start Arena</a>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        <button class="block w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-md transition-colors text-center shadow-md" onclick="window.location.href='{{ route('quiz.create', ['arena' => true]) }}'">
            Create New Arena Quiz
        </button>
    </div>
</div>


