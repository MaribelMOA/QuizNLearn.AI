<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @if (!empty($isArenaPlayer))
                @include('layouts.player-arena-nav')
            @elseif (empty($hideNavigation))
                @include('layouts.navigation')
            @else
                @include('layouts.play-nav')
            @endif


            {{--            @if (empty($hideNavigation))--}}
{{--                @include('layouts.navigation')--}}
{{--            @else--}}
{{--                --}}{{-- Puedes aquí poner otra cosa si quieres, como otra navegación especial --}}
{{--                @include('layouts.play-nav')--}}
{{--            @endif--}}

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow {{ $isQuizPage ? 'sticky top-0 z-50 ':'' }}">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
    <script src="https://cdn.jsdelivr.net/npm/socket.io-client/dist/socket.io.min.js"></script>


</html>
