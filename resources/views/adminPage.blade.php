<x-app-layout>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Post forms -->
            @auth
            <x-post-forms />
            @endauth
            <livewire:drafts.delete />
            {{-- admin page --}}
            <div class="mt-6 max-w-5xl mx-auto px-3 sm:px-8">
                <div id="all-content" class="content-section" style="display: block;">
                    <livewire:admin.viewall />
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