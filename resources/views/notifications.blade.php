<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Notifications --}}
            <div class="max-w-5xl mx-auto px-3 sm:px-8">
                <div class="tabs p-6 text-gray-100 dark:text-gray-100 rounded-lg mb-5 font-bold">
                    Toutes les notifications
                </div>
                <div class="block">
                    <livewire:notifications.viewall />
                </div>
            </div>
        </div>
    </div>

    <style>
        .tabs {
            display: flex;
            justify-content: space-around;
            background-color: #2d3748;
            padding: 20px;
        }
    </style>
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
        });
    </script>
</x-app-layout>