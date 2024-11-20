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
}; ?>

<div>
    @foreach ($matchedUsers as $matchedUser)
        <div class="max-w-5xl mx-auto px-3 sm:px-8 2xl:mt-0">
            <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full">
                <div class="flex sm:items-center items-start sm:flex-row flex-col sm:pt-0 pt-4">
                    <div class="flex flex-row gap-6">
                        <!-- Image de profil -->
                        <img src="{{ $matchedUser->getAvatar() }}" alt="Profile Image" class="w-24 h-24 rounded-full shadow-xl ring-4 ring-gray-200 dark:ring-gray-700">
        
                        {{-- Nom et Abonnés / Abonnement --}}
                        <div class="mr-2">
                            <div class="flex flex-row gap-3">
                                <h2 class="text-xl font-semibold text-black dark:text-gray-100 mr-2">{{ $matchedUser->name }}</h2>
                                
                                @auth
                                @if (auth()->user()->id !== $matchedUser->id)
                                <livewire:user.follow id="{{ $matchedUser->id }}" />
                                @endif
                                @endauth
        
                                @if($matchedUser->isBanned())
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
                                    <livewire:user.blocked-user-check id="{{ $matchedUser->id }}" />
                                @endif
                            </div>
                            <div class="flex mb-4">
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
                            {{-- Biographie --}}
                            <div class="mt-4">
                                <div class="flex flex-col">
                                    <div id="bio-reduite" class="bio-reduite text-gray-600 dark:text-gray-300">
                                        <p>{{ $matchedUser->bio ?? '' }}</p>
                                    </div>
                                </div>
                            </div>                                              
                        </div>
                    </div>
                    
                    {{-- Éditer profil --}}
                    @auth
                    @if (auth()->user()->id == $matchedUser->id)
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
                                <button>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-7 text-gray-800 hover:text-gray-950 dark:text-gray-100 dark:hover:text-gray-300">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                </button>
                            </x-slot>
        
                            <x-slot name="content">
                                <ul class="py-1 text-sm min-h-fit text-gray-200">
                                    <li>
                                        <a href="{{ url('messages/user/' . $matchedUser->id) }}"
                                            title="Envoyer un message à {{$matchedUser->name}} ?"
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
                                        <livewire:user.block-user :userId="$matchedUser->id" />
                                        @endauth
                                    </li>
                                </ul>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endif
                    @endauth
                </div>
                
            </div>
        </div>
    @endforeach
    <style>
        .bio-reduite p {
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
            white-space: pre-wrap;
        }
    </style>
</div>

