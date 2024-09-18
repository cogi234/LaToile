<div class="max-w-5xl mx-auto px-3 sm:px-8">
    <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-4 md:p-5 p-2 md:mb-5 mb-3 w-full">
        <div class="flex items-center ">
            <!-- Image de profil -->
            <img src="{{ asset($user->avatar ?? 'images/default-avatar.jpg') }}" alt="Profile Image"
                class="w-20 h-20 rounded-full mr-4 shadow-lg">

            {{-- Nom et Abonnés / Abonnement --}}
            <div>
                <h2 class="text-xl font-semibold text-black dark:text-gray-100">{{ $user->name }}</h2>
                <p class="text-black dark:text-gray-100">Abonnés : {{ $user->followers()->count() }}</p>
                <p class="text-black dark:text-gray-100">Abonnements : {{ $user->followed_users()->count() }}</p>
            </div>
        </div>
        {{-- Biographie + Modérateur? --}}
    <div class="mt-4">
        <p class="text-gray-600 dark:text-gray-300">{{ $user->bio ?? 'Aucune biographie disponible.' }}</p>
        @if($user->moderator)
            <div class="flex flex-row items-center">
                <p class="text-green-500 font-bold">Modérateur</p>
                <div title="Modérateur vérifié">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-green-500 ml-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
            </div>
        @endif
    </div>
    </div>
</div>

{{-- Publications --}}
<div class="mt-6 max-w-5xl mx-auto px-3 sm:px-8">
    <div class="tabs p-6 text-gray-100 dark:text-gray-100 rounded-lg mb-5 font-bold">
        Tous les posts de {{ $user->name }}
    </div>
    <div id="all-content" class="content-section" style="display: block;">
        <livewire:posts.view-specific-user :userId="$user->id" />
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

<x-script-showPostEditor />