<x-app-layout>

    <div class="max-w-3xl mx-auto p-6 mt-10 bg-white rounded-md shadow-md text-center">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">¡Modo Estudio Finalizado!</h2>

        <p class="text-xl text-gray-600 mb-4">
            Tiempo Total: {{ gmdate('i:s', $totalTimeSeconds) }}
        </p>
        <p class="text-xl text-emerald-600 mb-8">
            XP Ganado: {{ $xpGained }} XP
        </p>

        <a href="{{ route('quizzes.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded">
            Volver a Quizzes
        </a>
    </div>

</x-app-layout>
