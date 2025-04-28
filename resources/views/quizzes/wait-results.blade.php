<x-app-layout>

    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-green-500 border-solid mb-6"></div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">We’re preparing your results</h2>
        <p class="text-gray-600 mb-6">This may take a few seconds. Thanks for your patience.</p>

        <div class="text-sm text-gray-500 italic">
            Please don’t close this window. You’ll be redirected automatically when it’s ready.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.onload = function () {
            fetch("{{ route('quizzes.showQuizResults') }}", {
                method: "GET",
                headers: {
                    'Accept': 'text/html',
                }
            }).then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    window.location.href = "{{ route('quizzes.showQuizResults') }}";
                }
            }).catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred while preparing the results. Redirecting you back.',
                    confirmButtonText: 'OK',
                    didClose: () => {
                        window.location.href = "{{ route('quizzes.index') }}";
                    }
                });
            });
        };
    </script>

</x-app-layout>

