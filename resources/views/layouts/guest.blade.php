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

        <!-- Scripts -->
        <x-dark-light-mode-script />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- Twemoji -->
        <script src="https://unpkg.com/twemoji@latest/dist/twemoji.min.js" crossorigin="anonymous"></script>
        <script>
            function parseEmoji() {
                if (typeof twemoji !== "undefined" && typeof twemoji.parse === "function") {
                    // Parse the document body to replace emoji codes with images
                    twemoji.parse(document.body, {
                        base: 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/',
                        folder: '72x72/',
                        ext: '.png'
                    });
                } else {
                    console.error("Twemoji library did not load correctly.");
                }
            }
            document.addEventListener("DOMContentLoaded", function() {
                // Appeler parseEmoji pour initialiser les emojis à la première chargement
                parseEmoji();

                // Initialiser MutationObserver pour surveiller les ajouts dans le DOM
                const observer = new MutationObserver((mutations) => {
                    setTimeout(() => {
                        parseEmoji();
                    }, 500);
                });

                observer.observe(document.body, { childList: true, subtree: true });
            });
        </script>

    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>