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
            text: "Your play mode use will be reduced if you've answered more that half the quesitons",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, exit',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                {{--window.location.href = "{{ route('quizzes.index') }}";--}}
                // Obtener el modo desde la sesión de Laravel (inyectado en blade)
                let mode = "{{ session('game_mode') }}";
                let quizId = "{{ session('current_quiz_id') }}";

                // Solo enviar petición si es Study o Arena
                if (mode === 'Study' || mode === 'Arena' && quizId) {
                    if (mode === 'Arena') {
                        const arenaGameId = "{{ session('arena_game_id') }}";
                        axios.post(`/arena/${arenaGameId}/finish-game`)
                            .then(() => {
                                window.location.href = "{{ route('quizzes.index') }}";
                            })
                            .catch(() => {
                                Swal.fire('Oops', 'An error occurred while exiting Arena mode.', 'error');
                            });
                    } else {
                        axios.post(`/quizzes/play/exit/${quizId}`, {
                            mode: mode
                        }).then(() => {
                            window.location.href = "{{ route('quizzes.index') }}";
                        }).catch(() => {
                            Swal.fire('Oops', 'An error occurred while exiting.', 'error');
                        });
                    }
                } else {
                    // Si es Quiz mode, solo redirige sin guardar
                    window.location.href = "{{ route('quizzes.index') }}";
                }
            }
        });
    });

</script>

