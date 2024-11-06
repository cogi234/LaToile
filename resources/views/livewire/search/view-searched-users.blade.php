<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {

    public $query;
    public $matchedUsers = [];
    public $moreAvailable = true;

    public function mount($query)
    {
        $this->query = $query;
        $this->matchedUsers = User::where('name', 'like', '%' . $this->query . '%')
        ->where('id', '!=', auth()->id())
        ->get();
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $matchedUsers = User::where('name', 'like', '%' . $this->query . '%')->get();

            // Check if there are more pages to load
            $this->moreAvailable = $matchedUsers->count() == 10;
        }
    }

    public function resetUsers(){
        // Check if there are more pages to load
        $this->moreAvailable = $this->matchedUsers->isNotEmpty();
    }
}; ?>

<div>
    @foreach ($matchedUsers as $matchedUser)
    <div class="max-w-5xl mx-auto px-3 sm:px-8">
        <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-4 md:p-5 p-2 md:mb-5 w-full">
            <div class="flex items-center justify-between">
                <!-- Image de profil et détails -->
                <div class="flex items-center">
                    <!-- Image de profil -->
                    <img src="{{ $matchedUser->getAvatar() }}" alt="Profile Image"
                        class="w-20 h-20 rounded-full mr-4 shadow-lg">

                    <!-- Nom et Abonnés / Abonnement -->
                    <div>
                        <div class="flex flex-row">
                            <a href="/user/{{$matchedUser->id}}" class="hover:underline mr-2">
                                <h2 class="text-xl font-semibold text-black dark:text-gray-100">{{ $matchedUser->name }}
                                </h2>
                            </a>
                            @auth
                            @if (auth()->user()->id !== $matchedUser->id)
                            <livewire:user.follow id="{{ $matchedUser->id }}" />
                            @endif
                            @endauth
                        </div>
                        <p class="text-black dark:text-gray-100">Abonnés : {{ $matchedUser->followers()->count() }}</p>
                        <p class="text-black dark:text-gray-100">Abonnements : {{
                            $matchedUser->followed_users()->count() }}</p>
                    </div>
                </div>
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
                                <a href="{{ url('messages/user/' . $matchedUser->id) }}"
                                    title="Envoyer un message à {{$matchedUser->name}} ?"
                                    class="flex px-4 py-2 hover:bg-gray-600 items-center">
                                    <span class="mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 12.76c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.076-4.076a1.526 1.526 0 0 1 1.037-.443 48.282 48.282 0 0 0 5.68-.494c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
                                        </svg>
                                    </span>
                                    <span>Envoyer un message</span>
                                </a>
                            </li>
                            <li>
                                @auth
                             <livewire:user.block-user :userId="$matchedUser->id" />
                                @endauth
                            </li>
                        </ul>
                    </div>
                    <!-- Bouton Suivre/Désuivre -->
                    {{-- <div>
                        @if (auth()->user()->id !== $matchedUser->id)
                        <livewire:user.follow id="{{ $matchedUser->id }}" />
                        @endif
                    </div> --}}
                </div>
            </div>

            <!-- Biographie + Modérateur -->
            <div class="mt-4">
                <p class="text-gray-600 dark:text-gray-300">{{ $matchedUser->bio}}</p>
                @if($matchedUser->moderator)
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
        </div>
    </div>
    @endforeach

    @if ($moreAvailable)
    <x-primary-button wire:click='loadMore'
        class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6 mr-3">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
        </svg>

        Charger plus d'utilisateurs
    </x-primary-button>
    @endif
</div>

{{-- <style>
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
</style>

<script>
    function toggleDropdown() {
        var menu = document.getElementById("dropdownMenu");
        menu.classList.toggle("hidden");
    }

    window.onclick = function(event) {
        if (!event.target.closest('button')) {
            var dropdown = document.getElementById("dropdownMenu");
            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
            }
        }
    }
</script> --}}