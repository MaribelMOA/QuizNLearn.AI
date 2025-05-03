<div class="p-6 h-full flex flex-col">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Arena Mode</h2>
        <p class="text-gray-600">Play and learn with your friends simultaniously!</p>
    </div>

    <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-lg p-6 mb-8">
        <form action="{{ route('arena.join') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="player_name" class="block text-sm font-medium text-gray-700">Player Name:</label>
                <input type="text" name="player_name" id="player_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            </div>

            <div class="mb-4">
                <label for="pin" class="block text-sm font-medium text-gray-700">Game PIN:</label>
                <input type="text" name="pin" id="pin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm uppercase" required>
            </div>

            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-md">
                Join Game
            </button>

{{--            @if ($errors->any())--}}
{{--                <div class="alert alert-danger">--}}
{{--                    <ul>--}}
{{--                        @foreach ($errors->all() as $error)--}}
{{--                            <li>{{ $error }}</li>--}}
{{--                        @endforeach--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--            @endif--}}

        </form>
    </div>

    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-2">Start New Arena</h3>
        <p class="text-gray-600 text-sm mb-4">Create new game and share with friends</p>
    </div>

{{--    <div class="mt-auto">--}}
{{--        <a href="{{ route('arena.create') }}" class="block w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-md transition-colors text-center shadow-md">--}}
{{--            Create game--}}
{{--        </a>--}}
{{--    </div>--}}
    <button
        onclick="handleArenaCreate({{ $arenaModeUses }})"
        class="block w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-medium rounded-md transition-colors text-center shadow-md">
        Create game
    </button>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">You have <span class="font-semibold text-purple-600">{{ $arenaModeUses }}</span> Arena mode uses left</p>
    </div>
</div>
<script>
    function handleArenaCreate(usesLeft) {
        if (usesLeft <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Youve reached your limit',
                text: 'You have no Arena Mode uses left. Try upgrading plan or buying Arena mode uses.',
                confirmButtonColor: '#6366F1'
            });
           // alert("You have no Arena Mode uses left. Upgrade your plan to continue.");
        } else {
            window.location.href = "{{ route('quizzes.choose-quiz') }}";
        }
    }
</script>

