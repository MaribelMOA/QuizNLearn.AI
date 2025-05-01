<x-app-layout>

    <div class="max-w-3xl mx-auto p-6 mt-10 bg-white rounded-md shadow-md text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">¡Study Mode Finalized!</h2>

        <p class="text-xl text-gray-600 mb-4">
{{--            @php--}}
{{--                $hours = floor($totalTimeSeconds / 3600);--}}
{{--                $minutes = floor(($totalTimeSeconds % 3600) / 60);--}}
{{--                $seconds = $totalTimeSeconds % 60;--}}
{{--            @endphp--}}

{{--            Total Time: {{ $hours }}h {{ $minutes }}m {{ $seconds }}s--}}
            ⏱️ Total time in Study mode: {{ gmdate('i:s', $totalTimeSeconds) }}
        </p>


        <p class="text-xl text-emerald-600 mb-8">
            XP Gained: {{ $xpGained }} XP
        </p>

        <a href="{{ route('quizzes.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded">
            Return to Quizz List
        </a>
    </div>


</x-app-layout>
