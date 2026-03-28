<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Crear Juego en Modo Arena') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Selecciona un cuestionario para el Modo Arena</h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($questionnaires as $questionnaire)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow">
                                    <div class="p-6">
                                        <h4 class="text-lg font-semibold text-gray-800 mb-2">{{ $questionnaire->title }}</h4>
                                        <p class="text-gray-600 mb-4">{{ $questionnaire->questions_count }} preguntas</p>

                                        <div class="flex justify-end">
                                            <a href="{{ route('arena.setup', $questionnaire) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors">
                                                Seleccionar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
