<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Post forms -->
            <x-post-forms />
            <livewire:drafts.delete />
            {{-- Drafts --}}
            <div class="mt-6 max-w-5xl mx-auto px-3 sm:px-8">
                <div class="tabs p-6 text-gray-100 dark:text-gray-100 rounded-lg mb-5 font-bold">
                    Tous les brouillons
                </div>
                <div class="block">
                    <livewire:drafts.view />
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
</x-app-layout>