<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-teal-600 to-teal-700 text-white p-4">
            <h1 class="text-2xl font-bold text-center">Generating Your Quiz</h1>
        </div>

        <div class="p-8">
            <div class="flex justify-center mb-8">
                <div class="relative w-32 h-32">
                    <svg class="w-full h-full" viewBox="0 0 100 100">
                        <circle
                            class="text-gray-200"
                            stroke-width="8"
                            stroke="currentColor"
                            fill="transparent"
                            r="42"
                            cx="50"
                            cy="50"
                        />
                        <circle
                            class="text-teal-600 animate-[dash_1.5s_ease-in-out_infinite]"
                            stroke-width="8"
                            stroke-dasharray="264"
                            stroke-dashoffset="125"
                            stroke-linecap="round"
                            stroke="currentColor"
                            fill="transparent"
                            r="42"
                            cx="50"
                            cy="50"
                        />
                    </svg>
                    <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center">
                        <span class="text-2xl font-bold text-teal-700" id="progress-percentage">{{ $progress ?? '0%' }}</span>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <p class="text-gray-600 mb-4" id="status-message">{{ $message ?? 'Analyzing your content...' }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                    <div
                        class="bg-teal-600 h-2 rounded-full transition-all duration-300 ease-out"
                        style="width: {{ $progress ?? '0%' }}"
                    ></div>
                </div>
                <p class="text-sm text-gray-500">This may take a minute or two depending on the content size.</p>
            </div>

            <div class="mt-8 flex justify-center">
                <div class="flex space-x-2">
                    <div class="w-2 h-2 bg-teal-600 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-teal-600 rounded-full animate-pulse" style="animation-delay: 200ms"></div>
                    <div class="w-2 h-2 bg-teal-600 rounded-full animate-pulse" style="animation-delay: 400ms"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes dash {
        0% {
            stroke-dashoffset: 264;
        }
        50% {
            stroke-dashoffset: 66;
        }
        100% {
            stroke-dashoffset: 264;
        }
    }
</style>
