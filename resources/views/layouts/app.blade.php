<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LaToile') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

        <!-- Scripts -->
        <x-dark-light-mode-script />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js"></script>
        <!-- Twemoji -->
        <script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>


    </head>
    <body class="font-sans antialiased">
        <!-- App Layout -->
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Page Heading -->
            @auth
                <livewire:layout.navigation />
            @endauth
            @guest
                <livewire:layout.guest />
            @endguest

            <!-- Page Content -->
            <main class="min-h-screen">
                {{ $slot }}
            </main>
            
            @if (!isset($showFooter) || $showFooter == "true")
            <footer class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700">
                <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center py-6 dark:text-gray-300">
                    <a class="text-black dark:text-gray-300 underline" href="{{ route('about') }}">
                        À propos de ce site
                    </a>
                </div>
            </footer>
            @endif
        </div>
    </body>
</html>

<style>
    img.emoji {
        width: 20px; /* ajustez la taille ici */
        height: 20px; /* maintenez les proportions */
        display: inline-block;
        margin-right: 2px;
    }

    img.twemoji {
        width: 20px; /* ajustez la taille ici */
        height: 20px; /* maintenez les proportions */
        display: inline-block;
        margin-right: 2px;
    }

    svg.twemoji {
        width: 20px; /* ajustez la taille ici */
        height: 20px; /* maintenez les proportions */
        display: inline-block;
        margin-right: 2px;
    }

    .twemoji {
        width: 20px; /* ajustez la taille ici */
        height: 20px; /* maintenez les proportions */
        display: inline-block;
        margin-right: 2px;
    }
</style>
