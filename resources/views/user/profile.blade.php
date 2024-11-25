<x-app-layout>

    @php
        $backgroundImageStyle = "";
        if ($user->profile_background != null)
            $backgroundImageStyle = "background-image: url('" . Storage::url($user->profile_background) . "');";
    @endphp

    <div class="py-12" style="{{ $backgroundImageStyle }} background-size: cover; background-repeat: no-repeat; background-position: center; min-height: 100dvh">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-user-view :user="$user" />
        </div>
    </div>
    
    <!-- Post forms -->
    @auth
    <x-post-forms />
    @endauth
</x-app-layout>
