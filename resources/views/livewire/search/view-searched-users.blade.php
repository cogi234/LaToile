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
            <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-4 md:p-5 p-2 md:mb-5 mb-3 w-full">
                <div class="flex items-center justify-between">
                    <!-- Image de profil et détails -->
                    <div class="flex items-center">
                        <!-- Image de profil -->
                        <img src="{{ $matchedUser->getAvatar() }}" alt="Profile Image"
                            class="w-20 h-20 rounded-full mr-4 shadow-lg">

                        <!-- Nom et Abonnés / Abonnement -->
                        <div>
                            <a href="/user/{{$matchedUser->id}}" class="hover:underline"><h2 class="text-xl font-semibold text-black dark:text-gray-100">{{ $matchedUser->name }}</h2></a>
                            <p class="text-black dark:text-gray-100">Abonnés : {{ $matchedUser->followers()->count() }}</p>
                            <p class="text-black dark:text-gray-100">Abonnements : {{ $matchedUser->followed_users()->count() }}</p>
                        </div>
                    </div>
                    <div class="sm:mt-1 mt-5 sm:!ml-auto !ml-0 items-start self-start dark:!text-gray-100 transition duration-300 ease-in-out">
                        <button onclick="toggleDropdown()" class="focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 12h.01M12 12h.01M18 12h.01" />
                            </svg>
                        </button>
                        <div id="dropdownMenu" class="hidden absolute top-32 right-72 bg-gray-700 dark:bg-gray-900 rounded-md shadow-lg z-50 max-h-48 overflow-auto">
                            <ul class="py-1 text-sm min-h-fit text-gray-200">
                                <li>
                                    <a href="#" class="flex px-4 py-2 hover:bg-gray-600 items-center">
                                        <span class="mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                            </svg>                                              
                                        </span>
                                        <span>Suivre {{ $matchedUser->name }}</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="flex px-4 py-2 hover:bg-gray-600 items-center">
                                        <span class="mr-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>                                                                                            
                                        </span>
                                        <span>Envoyer un message</span>
                                    </a>
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
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-green-500 ml-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
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