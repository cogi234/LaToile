<div class="max-w-5xl mx-auto pt-5 px-3 sm:px-8 mt-10 2xl:mt-0">
    <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full">
        <div class="flex sm:items-center items-start sm:flex-row flex-col sm:pt-0 pt-4">
            <!-- Image de profil -->
            <div class="flex flex-row">
                <img src="{{ $user->getAvatar() }}" alt="Profile Image" class="w-20 h-20 rounded-full mr-4 shadow-lg">


                {{-- Nom et Abonnés / Abonnement --}}
                <div class="mr-2">
                    <div class="flex flex-row">
                        <h2 class="text-xl font-semibold text-black dark:text-gray-100 mr-2">{{ $user->name }}</h2>
                        @auth
                        @if (auth()->user()->id !== $user->id)
                        <livewire:user.follow id="{{ $user->id }}" />
                        @endif
                        @endauth
                    </div>
                    <div class="flex space-x-4 mt-2">
                        <!-- Lien vers les abonnements -->
                        <a href="/followings/{{$user->id}}"
                            class="hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg transition duration-150 ease-in-out">
                            <p
                                class="text-black dark:text-gray-100 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">
                                Abonnements : {{ $user->followed_users()->count() }}
                            </p>
                        </a>
                        <!-- Lien vers les abonnés -->
                        <a href="/followers/{{$user->id}}"
                            class="hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg transition duration-150 ease-in-out">
                            <p
                                class="text-black dark:text-gray-100 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">
                                Abonnés : {{ $user->followers()->count() }}
                            </p>
                        </a>
                    </div>
                    <div class="flex space-x-4 mt-2">
                        <button onclick="toggleFollowedTagsMenu()"
                            class="hover:bg-gray-100 dark:hover:bg-gray-700 px-4 py-2 rounded-lg transition duration-150 ease-in-out">
                            <p
                                class="text-black dark:text-gray-100 font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">
                                Tags suivis
                            </p>
                        </button>
                        <livewire:user.view-followedTags :userId="$user->id" wire:key='followedTags' />
                    </div>
                </div>
                {{-- <p class="text-black dark:text-gray-100">Abonnés : {{ $user->followers()->count() }}</p>
                <p class="text-black dark:text-gray-100">Abonnements : {{ $user->followed_users()->count() }}</p> --}}
                @if (Auth::Check())
                <livewire:user.blocked-user-check id="{{ $user->id }}" />
                @endif
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
            <div
                class="sm:mt-1 mt-5 sm:!ml-auto !ml-0 items-start self-start dark:!text-gray-100 transition duration-300 ease-in-out">
                <button onclick="toggleDropdown()" title="Plus d'actions..." class="focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="size-7 text-gray-800 hover:text-gray-950 dark:text-gray-100 dark:hover:text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </button>
                <div id="dropdownMenu"
                    class="hidden absolute top-32 right-72 bg-gray-700 dark:bg-gray-900 rounded-md shadow-lg z-50 max-h-48 overflow-auto">
                    <ul class="py-1 text-sm min-h-fit text-gray-200">
                        <li>
                            <a href="{{ url('messages/user/' . $user->id) }}"
                                title="Envoyer un message à {{$user->name}} ?"
                                class="flex px-4 py-2 hover:bg-gray-600 items-center">
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
                </div>
            </div>
            @endif
            @endauth
        </div>
        {{-- Biographie + Modérateur? --}}
        <div class="mt-4">
            <div class="flex flex-col">
                <div id="bio-reduite" class="bio-reduite text-gray-600 dark:text-gray-300">
                    <p> {{ $user->bio ?? '' }} </p>
                </div>
                <div id="bio-complete" class="bio-complete hidden text-gray-600 dark:text-gray-300">
                    <p> {{ $user->bio ?? '' }} </p>
                </div>
                @if ($user->bio != '' && strlen($user->bio) > 180)
                <button id="toggle-bio" onclick="toggleBio()" title="...Voir le reste de la bio"
                    class="mt-2 px-4 py-2 w-fit justify-self-center self-center bg-gray-500 text-white rounded hover:bg-gray-600">
                    <svg id="icon-bio" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                    </svg>
                </button>
                @endif
            </div>
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
        text-overflow:
            ellipsis;
        display:
            -webkit-box;
        -webkit-line-clamp: 3;
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
    function toggleDropdown() {
        var menu = document.getElementById("dropdownMenu");
        menu.classList.toggle("hidden");
    }

    function toggleBio() { 
        const bioReduite = document.getElementById("bio-reduite"); 
        const bioComplete = document.getElementById("bio-complete"); 
        const iconBio = document.getElementById("icon-bio"); 

        // Vérifier si l'état est "réduit"
        const isHidden = bioReduite.classList.contains("hidden");

        if (isHidden) { 
            bioReduite.classList.remove("hidden"); 
            bioComplete.classList.add("hidden"); 
            iconBio.innerHTML = `
                <svg id="icon-bio" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                </svg>`;
        } else { 
            bioReduite.classList.add("hidden"); 
            bioComplete.classList.remove("hidden"); 
            iconBio.innerHTML = `
                <svg id="icon-bio" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 18.75 7.5-7.5 7.5 7.5" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 7.5-7.5 7.5 7.5" />
                </svg>`;
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