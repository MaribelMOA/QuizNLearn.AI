<div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-all duration-300 flex flex-col h-full">
    <!-- Decorative Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 w-full"></div>

    <!-- Card Header -->
    <div class="p-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            <div class="bg-indigo-100 p-1.5 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div class="text-xs text-gray-500 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ $summary->created_at->format('M d, Y') }}
                <span class="ml-1 text-xs text-gray-400">({{ $summary->created_at->diffForHumans() }})</span>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            <form action="{{ route('summaries.destroy', $summary) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-gray-400 hover:text-rose-500 transition-colors p-1 rounded-full hover:bg-rose-50"
                        onclick="return confirm('Are you sure you want to delete this summary?')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    title: '¡Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonText: 'Accept'
                });
            });
        </script>
    @endif

    <!-- Card Content -->
    <div class="px-4 py-3 flex-grow">
        <h3 class="text-lg font-semibold text-gray-800 mb-2 line-clamp-2 hover:text-indigo-600 transition-colors">
            {{ $summary->title }}
        </h3>

        <div class="bg-gray-50 rounded-md p-3 mb-3 border border-gray-100">
            <p class="text-sm text-gray-600 line-clamp-3">
                {{ \Illuminate\Support\Str::limit(strip_tags($summary->content), 150) }}
            </p>
        </div>

        <div class="flex items-center text-xs text-gray-500 mt-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
            </svg>
            {{ \Illuminate\Support\Str::wordCount(strip_tags($summary->content)) }} words

            @if(isset($summary->category) && $summary->category)
                <span class="mx-2">•</span>
                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                    {{ $summary->category }}
                </span>
            @endif
        </div>
    </div>

    <!-- Card Footer -->
    <div class="p-4 border-t border-gray-100 mt-auto bg-gray-50">
        <a href="{{ route('summaries.details', $summary) }}"
           class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            View Details
        </a>
    </div>
</div>

