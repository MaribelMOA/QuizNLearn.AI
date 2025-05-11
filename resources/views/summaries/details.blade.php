<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Summaries') }}
        </h2>
    </x-slot>

    <div class="py-6 ">

        <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-500 bg-opacity-75 flex items-center justify-center p-4" onclick="redirectToSummaries(event)">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">

                <div class="flex justify-between items-center border-b p-4">
                    <div class="flex items-center">
                        <span class="text-2xl mr-2">📋</span>
                        <h3 class="text-2xl font-bold text-gray-800">Summary Info</h3>
                    </div>
                    <!-- Button to close and redirect -->
                    <a href="{{ route('summaries.index') }}" class="text-red-500 hover:text-red-700"> <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                                                                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                </div>

                <div class="p-6">


                    <div class="mb-8">
                        <div class="flex justify-between items-start">
                            <div class="flex items-center mb-4">
                                <span class="text-xl mr-2">📌</span>
                                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $summary->title }}</h2>

                            </div>
                            <form action="{{ route('summaries.destroy', $summary) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit"  class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md flex items-center transition-colors" onclick="return confirm('Are you sure you want to delete this summary?')">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                        />
                                    </svg>
                                    Delete
                                </button>

                            </form>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                            <div>
                                <div class="flex items-center mb-3">
                                    <span class="text-gray-500 mr-2">📅</span>
                                    <span class="text-gray-600">Created: </span>
                                    <span class="ml-1 font-medium">{{ $summary->created_at->format('M d, Y')  }}</span>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="mt-6 mb-12 prose max-w-none">
                        {!! nl2br(e($summary->content)) !!}
                    </div>



                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <span class="text-xl mr-2">⬇️</span>
                            <h4 class="text-xl font-semibold text-gray-700">Download summary</h4>
                        </div>


                        <!-- Contenedor para los botones con flexbox centrado y márgenes -->
                        <div class="flex justify-center gap-6">
                            <!-- Botón PDF con icono -->
                            <a href="{{ route('summaries.downloadPdf', ['summary_id' => $summary->id]) }}"
                               class="bg-green-600 hover:bg-green-700 text-white w-full max-w-xs px-8 py-4 rounded-md flex items-center justify-center transition-colors border border-green-700">
                                <!-- Icono PDF -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7v10M13 7l4 4m-4-4l-4 4" />
                                </svg>
                                Download PDF
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        // Función para redirigir al usuario a quizzes.index si hace clic fuera del modal
        function redirectToSummaries(event) {
            // Verifica si el clic fue fuera del modal
            if (event.target === event.currentTarget) {
                window.location.href = "{{ route('summaries.index') }}";
                // Redirige a quizzes.index
            }
        }


    </script>
</x-app-layout>
