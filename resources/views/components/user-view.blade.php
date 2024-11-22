<div class="max-w-5xl mx-auto pt-5 px-3 sm:px-8 mt-10 2xl:mt-0">
    <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full">
        <div class="flex sm:items-center items-start flex-row sm:pt-0 pt-4">
            <div class="flex flex-row">
                <!-- Image de profil -->
                <img src="{{ $user->getAvatar() }}" alt="Profile Image" class="w-20 h-20 rounded-full mr-4 shadow-lg">

                {{-- Nom et Abonnés / Abonnement --}}
                <div class="mr-2">
                    <div class="flex flex-row gap-3">
                        <h2 class="text-xl font-semibold text-black dark:text-gray-100 mr-2">{{ $user->name }}</h2>
                        
                        @auth
                        @if (auth()->user()->id !== $user->id)
                        <livewire:user.follow id="{{ $user->id }}" />
                        @endif
                        @endauth

                        @if($user->isBanned())
                            <div class="flex flex-row items-center">
                                <span class="text-red-500 font-bold mr-1">Banni</span>
                                <div title="Banni">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                        stroke="currentColor" class="size-4 text-red-500 mr-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                        @if (Auth::Check())
                            <livewire:user.blocked-user-check id="{{ $user->id }}" />
                        @endif
                    </div>
                    <div class="flex mb-4">
                        @if($user->moderator)
                            <div class="flex flex-row items-center">
                                <p class="text-green-500 font-bold">Modérateur</p>
                                <div title="Modérateur vérifié">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor" class="size-4 text-green-500 ml-1">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                    </svg>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="flex md:flex-row flex-col space-x-4 mt-2">
                        <!-- Lien vers les abonnements -->
                        <a href="/followings/{{$user->id}}" title="Voir les abonnements de {{$user->name}}"
                            class="flex mb-3 !ml-0 mr-0 md:!mr-4 md:mb-0 items-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-lg transition duration-150 ease-in-out shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>   
                            <span class="text-black dark:text-gray-100 font-semibold">Abonnements : {{ $user->followed_users()->count() }}</span>
                        </a>
                        <!-- Lien vers les abonnés -->
                        <a href="/followers/{{$user->id}}" title="Voir les abonnés de {{$user->name}}"
                            class="flex mb-3 mr-0 md:!mr-4 !ml-0 md:mb-0 items-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-lg transition duration-150 ease-in-out shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>                              
                            <span class="text-black dark:text-gray-100 font-semibold">Abonnés : {{ $user->followers()->count() }}</span>
                        </a>
                        <!-- Tags suivis -->
                        <button onclick="toggleFollowedTagsMenu()" title="Voir les tags suivis de {{$user->name}}"
                            class="flex !ml-0 mr-0 md:!mr-4 items-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 px-4 py-2 rounded-lg transition duration-150 ease-in-out shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2 text-indigo-600 dark:text-indigo-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5" />
                            </svg>                                  
                            <span class="text-black dark:text-gray-100 font-semibold">Tags suivis</span>
                        </button>
                        <!-- Contenu du menu des tags suivis -->
                        <livewire:user.view-followedTags :userId="$user->id" wire:key='followedTags' /> 
                    </div>
                    <div class="flex items-center mt-4 space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-indigo-600 dark:text-indigo-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5m7.5-1.5v1.5m-9 0h10.5c.621 0 1.125.504 1.125 1.125v13.5c0 .621-.504 1.125-1.125 1.125H6.75c-.621 0-1.125-.504-1.125-1.125V5.625c0-.621.504-1.125 1.125-1.125zm-.75 4.5h12" />
                        </svg>
                        <span class="text-gray-600 dark:text-gray-300 text-sm">Membre depuis le : {{ $user->created_at->format('d M Y') }}</span>
                    </div>                                                 
                </div>
            </div>
            
            {{-- Éditer profil --}}
            @auth
            @if (auth()->user()->id == $user->id)
            <x-primary-button :href="route('profile')" wire:navigate
                class="sm:mt-1 mt-5 sm:!ml-auto !ml-0 items-start self-start !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-4 mr-2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                </svg>

                Éditer le profil
            </x-primary-button>
            @else
            <div class="sm:mt-1 mt-5 sm:!ml-auto !ml-0 items-start self-start dark:!text-gray-100 transition duration-300 ease-in-out">
                <x-dropdown width="w-56">
                    <x-slot name="trigger">
                        <button title="Plus d'actions..." >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor"
                                class="size-7 text-gray-800 hover:text-gray-950 dark:text-gray-100 dark:hover:text-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content" class="bg-gray-800">
                        <ul class="py-1 text-sm min-h-fit text-gray-800 dark:text-gray-200">
                            <li>
                                <a href="{{ url('messages/user/' . $user->id) }}"
                                    title="Envoyer un message à {{$user->name}} ?"
                                    class="flex px-4 py-2 hover:bg-gray-200 hover:dark:bg-gray-600 items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                    </svg>
                                    <span>Envoyer un message</span>
                                </a>
                            </li>
                            <li>
                                @auth
                                <livewire:user.block-user :userId="$user->id" />
                                @endauth
                            </li>
                        </ul>
                    </x-slot>
                </x-dropdown>
            </div>
            @endif
            @endauth
        </div>
        {{-- Biographie --}}
        <div class="mt-4">
            <div class="flex flex-col">
                <div id="bio-reduite" class="bio-reduite text-gray-600 dark:text-gray-300">
                    <p>{{ $user->bio ?? '' }}</p>
                </div>
                <div id="bio-complete" class="bio-complete hidden text-gray-600 dark:text-gray-300">
                    <p>{{ $user->bio ?? '' }}</p>
                </div>
                @if ($user->bio && strlen($user->bio) > 180)
                <button id="toggle-bio" onclick="toggleBio()" title="Voir le reste de la bio"
                    class="mt-2 flex justify-center items-center">
                    <svg id="icon-bio" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white transition">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25l7.5 7.5 7.5-7.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5 7.5 7.5-7.5" />
                    </svg>                    
                </button>
                @endif
            </div>
            
        </div>
    </div>
</div>

{{-- Publications --}}
<div class="mt-6 max-w-5xl mx-auto px-3 sm:px-8">
    <div class="tabs p-6 text-gray-100 dark:text-gray-100 rounded-lg mb-5 font-bold">
        Tous les posts de {{ $user->name }}
    </div>
    <!-- Filtres (Newest/Popular) -->
    <div
        class="bg-white w-full lg:max-w-[50%] justify-self-center dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:mb-5">
        <div class="flex flex-col sm:flex-row p-2 sm:p-3 justify-self-center text-gray-900 dark:text-gray-100">
            <a href="javascript:void(0);"
                class="text-black dark:text-gray-100 dark:hover:bg-blue-100/20 hover:bg-gray-200 cursor-pointer px-4 py-3 font-bold text-center no-underline flex-grow rounded-lg transition-all duration-300 ease-in-out activeFilter"
                id="newest-tab" onclick="applyNewFilter('newest'); applyFilterToProfile('newest');">
                Les plus récents d'abord
            </a>
            <a href="javascript:void(0);"
                class="text-black dark:text-gray-100 dark:hover:bg-blue-100/20 hover:bg-gray-200 cursor-pointer px-4 py-3 font-bold text-center no-underline flex-grow rounded-lg transition-all duration-300 ease-in-out"
                id="popular-tab" onclick="applyNewFilter('popular'); applyFilterToProfile('popular');">
                Les plus populaires d'abord
            </a>
        </div>
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

    #dropdownMenu::-webkit-scrollbar {
        width: 8px;
    }

    #dropdownMenu::-webkit-scrollbar-thumb {
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 4px;
    }

    .bio-reduite p {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        white-space: pre-wrap;
    }


    .bio-complete p {
        white-space: pre-wrap;
    }

    .activeFilter {
        background-color: #8a969c3f;
        /* correspond à bg-gray-700 */
        border-bottom: 3px solid #00000027;
        /* correspond à text-blue-400 */
        transition: 0s;
    }
</style>

<script>
    function toggleBio() { 
        const bioReduite = document.getElementById("bio-reduite"); 
        const bioComplete = document.getElementById("bio-complete"); 
        const iconBio = document.getElementById("icon-bio"); 
        const toggleButton = document.getElementById("toggle-bio"); // Référence au bouton

        // Vérifiez si la version réduite est cachée
        const isHidden = bioReduite.classList.contains("hidden");

        if (isHidden) { 
            bioReduite.classList.remove("hidden"); 
            bioComplete.classList.add("hidden"); 

            // Icône : deux flèches vers le bas
            iconBio.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 5.25l7.5 7.5 7.5-7.5" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l7.5 7.5 7.5-7.5" />   
            `;
            // Change le titre du bouton
            toggleButton.title = "Voir le reste de la bio";
        } else { 
            bioReduite.classList.add("hidden"); 
            bioComplete.classList.remove("hidden"); 

            // Icône : deux flèches vers le haut
            iconBio.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 18.75l7.5-7.5 7.5 7.5" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12l7.5-7.5 7.5 7.5" />
            `;
            // Change le titre du bouton
            toggleButton.title = "Réduire la bio";
        }
    }


    // Filtres
    function applyNewFilter(filter) {
        // Save filter to localStorage
        localStorage.setItem('lastFilter', filter);

        // Reset Active Class
        document.getElementById('newest-tab').classList.remove('activeFilter');
        document.getElementById('popular-tab').classList.remove('activeFilter');

        // Add Active Class to Selected Tab
        document.getElementById(filter + '-tab').classList.add('activeFilter');
    }
</script>