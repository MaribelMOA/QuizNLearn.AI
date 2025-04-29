<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-24">
            <!-- Logo a la izquierda -->
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="block h-10 w-auto">
            </div>

            <!-- Botón Exit a la derecha -->
            <div class="flex items-center">
                <button id="close-quiz"
                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white text-base font-semibold rounded-lg hover:bg-red-600 transition-all shadow-md">
                    ✖ <span>Exit</span>
                </button>
            </div>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('close-quiz').addEventListener('click', function() {
        Swal.fire({
            title: 'Are you sure you want to exit?',
            text: "Your progress will be lost",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, exit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('quizzes.index') }}";
            }
        });
    });

</script>

