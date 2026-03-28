<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-24">
            <!-- Logo a la izquierda -->
            <div class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="block h-10 w-auto">
            </div>

            <!-- Botón Exit a la derecha -->
            <div class="flex items-center">
                <button id="exit-player"
                        class="flex items-center gap-2 px-4 py-2 bg-red-500 text-white text-base font-semibold rounded-lg hover:bg-red-600 transition-all shadow-md">
                    ✖ <span>Exit</span>
                </button>
            </div>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('exit-player').addEventListener('click', function () {
        Swal.fire({
            title: 'Are you sure you want to exit?',
            text: "If you exit, you'll be removed from the arena.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, exit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                let arenaId = "{{ session('arena_game_id') }}"; // Asegúrate de tener esta variable en sesión
                let userId = "{{ Auth::id() }}";

                axios.post(`/arena/${arenaId}/remove-player`, {
                    user_id: userId
                })
                    .then(() => {
                        window.location.href = "{{ route('quizzes.index') }}";
                    })
                    .catch(() => {
                        Swal.fire('Oops', 'Error removing player from arena.', 'error');
                    });
            }
        });
    });

</script>
