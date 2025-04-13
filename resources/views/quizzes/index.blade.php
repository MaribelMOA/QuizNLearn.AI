<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Quizzes') }}
            </h2>
            <!--<a href="{{ route('quizzes.create') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md hover:bg-blue-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Crear Nuevo
            </a>-->
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Section -->
            <div class="mb-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Quizzes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Total Cuestionarios</h3>
                        <div class="flex items-center">
                            <span class="text-4xl font-bold text-primary">{{ $totalQuizzes }}</span>
                            <span class="ml-2 text-sm text-gray-500">cuestionarios creados</span>
                        </div>
                    </div>
                </div>

                <!-- Available Quiz Creations -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Creaciones Disponibles</h3>
                        <div class="flex items-center">
                            <span class="text-4xl font-bold text-green-500">{{ $availableCreations }}</span>
                            <span class="ml-2 text-sm text-gray-500">creaciones restantes</span>
                        </div>
                    </div>
                </div>

                <!-- Available Uses -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Usos Disponibles</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-700">Modo Estudio</p>
                                <p class="text-2xl font-bold text-blue-600">{{ $studyModeUses }} <span class="text-sm font-normal">usos</span></p>
                            </div>
                            <div class="bg-purple-50 p-3 rounded-lg">
                                <p class="text-sm font-medium text-gray-700">Modo Arena</p>
                                <p class="text-2xl font-bold text-purple-600">{{ $arenaModeUses }} <span class="text-sm font-normal">usos</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="mb-8 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('quizzes.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                        <div class="flex-grow">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" name="search" value="{{ request('search') }}" class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" placeholder="Buscar por título, modo, nivel...">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <select name="difficulty" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="">Todos los niveles</option>
                                <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Fácil</option>
                                <option value="medium" {{ request('difficulty') == 'medium' ? 'selected' : '' }}>Medio</option>
                                <option value="hard" {{ request('difficulty') == 'hard' ? 'selected' : '' }}>Difícil</option>
                            </select>
                            <select name="mode" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                                <option value="">Todos los modos</option>
                                <option value="quiz" {{ request('mode') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                <option value="study" {{ request('mode') == 'study' ? 'selected' : '' }}>Estudio</option>
                                <option value="kahoot" {{ request('mode') == 'kahoot' ? 'selected' : '' }}>Kahoot</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition-colors">
                                Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Create New Quiz Card -->
                <a href="{{ route('quizzes.create') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-2 border-dashed border-gray-300 hover:border-primary transition-colors flex flex-col items-center justify-center p-10 text-center">
                    <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">Crear Nuevo Cuestionario</h3>
                    <p class="text-gray-500">Crea un nuevo cuestionario personalizado</p>
                </a>

                <!-- Quiz Cards -->
                @foreach($questionnaires as $questionnaire)
                    @include('quizzes._quiz-card', ['questionnaire' => $questionnaire])
                @endforeach
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
</x-app-layout>

