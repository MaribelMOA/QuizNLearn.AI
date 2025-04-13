<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Funcionalidades Disponibles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($features as $feature)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 transition-all duration-300 hover:-translate-y-2 hover:shadow-xl">
                                <div class="p-6 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 text-primary rounded-full mb-4">
                                        <i class="fas fa-star text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-4">{{ $feature['name'] }}</h3>
                                    <div class="text-2xl font-bold mb-2">
                                        {{ $feature['price'] }}
                                    </div>
                                    <p class="text-gray-600 mb-6">{{ $feature['description'] }}</p>

                                    <ul class="text-left space-y-3 mb-8">
                                        @foreach($feature['features'] as $item)
                                            <li class="flex items-start">
                                                <svg class="w-5 h-5 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                <span>{{ $item }}</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <a href="#" class="block w-full py-3 px-6 text-center bg-primary hover:bg-blue-600 text-white font-medium rounded-md transition-colors">
                                        Seleccionar Plan
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
