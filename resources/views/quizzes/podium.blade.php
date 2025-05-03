<x-app-layout >

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">Arena Mode</h1>
        </div>
    </x-slot>
    <div class="container text-center">
        <h1 class="text-3xl font-bold mb-6">🎉 Podio Final 🎉</h1>

        <ol class="text-xl">
            @foreach ($podium as $index => $player)
                <li>
                    <strong>#{{ $index + 1 }}:</strong> {{ $player['nickname'] }} - {{ $player['score'] }} pts
                </li>
            @endforeach
        </ol>
    </div>
</x-app-layout>
