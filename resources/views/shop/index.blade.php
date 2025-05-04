<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('XP Store') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

{{--        --}}{{-- Saldo actual de XP --}}
{{--        <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-xl">--}}
{{--            <h3 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Tus Puntos XP</h3>--}}
{{--            <p class="text-2xl text-indigo-600 dark:text-indigo-400 font-bold">{{ Auth::user()->xp_points }} XP</p>--}}
{{--        </div>--}}

        {{-- Usos disponibles --}}
        <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-xl">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Recursos Disponibles</h3>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-800 dark:text-gray-100">
                <li>Creaciones de cuestionarios: {{ $availableCreations }}</li>
                <li>Usos de modo estudio: {{ $studyModeUses }}</li>
                <li>Usos de modo arena: {{ $arenaModeUses }}</li>
                <li>Creaciones de resúmenes: {{ $availableSummaryCreations }}</li>
            </ul>
        </div>

        {{-- Mejora de plan --}}
        <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-xl">
            <a href="{{ route('profile.edit') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                Mejorar mi plan
            </a>
        </div>

        {{-- Comprar funcionalidades con XP --}}
        <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-xl">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Comprar Funcionalidades con XP</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($grouped as $type)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold">{{ $type->name }}</h2>
                        <p class="text-gray-600 mb-4">{{ $type->description }}</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($type->features as $feature)
                                <form method="POST" action="{{ route('xp.purchaseFeature') }}" class="confirm-purchase-form">
                                    @csrf
                                    <input type="hidden" name="feature_id" value="{{ $feature->id }}" >

                                    <button class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded shadow">
                                        {{ $feature->name }} ({{ $feature->xp_price }} XP)
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- Comprar XP con dinero --}}
        <div class="bg-white dark:bg-gray-800 p-6 shadow rounded-xl">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-100">Comprar Puntos XP</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($xpPrices as $package)
                    <div class="border p-4 rounded-lg bg-gray-100 dark:bg-gray-700">
                        <p class="text-xl font-semibold text-gray-800 dark:text-white">{{ $package->xp_amount }} XP</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">${{ number_format($package->price, 2) }} USD</p>
                        <form method="POST" action="{{ route('xp.purchasePackage') }} " class="confirm-purchase-form">
                            @csrf
                            <input type="hidden" name="xp_price_id" value="{{ $package->id }}">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                Comprar XP
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
    <script>
        @if (session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#3085d6',
        });
        @endif

        @if (session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
        });
        @endif

        @if (session('info'))
        Swal.fire({
            icon: 'info',
            title: 'Information',
            text: '{{ session('info') }}',
            confirmButtonColor: '#3085d6',
        });
        @endif

        document.querySelectorAll('.confirm-purchase-form').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent immediate submission

                Swal.fire({
                    title: 'Confirm Purchase?',
                    text: "Are you sure you want to proceed with this purchase?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, buy it',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit(); // Submit form if confirmed
                    }
                });
            });
        });
    </script>

</x-app-layout>
