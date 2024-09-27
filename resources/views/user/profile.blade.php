<x-app-layout>
    <!-- Post forms -->
    @auth
    <x-post-forms />
    @endauth

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-user-view :user="$user" />
        </div>
    </div>
</x-app-layout>