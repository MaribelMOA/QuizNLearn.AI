<x-app-layout>

    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-blue-500 border-solid mb-6"></div>

        <h2 class="text-2xl font-bold text-gray-800 mb-2">We’re generating your summary</h2>
        <p class="text-gray-600 mb-6">This may take a few seconds. Thanks for your patience.</p>

        <div class="text-sm text-gray-500 italic">
            Please don’t close this window. You’ll be redirected automatically when it’s ready.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        window.onload = function () {
            fetch("{{ route('summaries.process', $summary->id) }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Summary created!',
                            text: 'Congrats! You re summary has been created.We will redirect you to your Summary List',
                            confirmButtonText: 'OK',
                            didClose: () => {
                                window.location.href = "{{ route('summaries.index') }}";
                            }
                        });

                    } else {
                        // SweetAlert on error
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'An error occurred while generating the summary. We will redirect you to your Summary List',
                            confirmButtonText: 'OK',
                            didClose: () => {
                                window.location.href = "{{ route('summaries.index') }}";
                            }
                        });
                    }
                });
        };
    </script>
</x-app-layout>
