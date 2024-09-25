<div {{ $attributes->merge(['class' =>"post-header flex items-center"]) }}>
    <!-- Image de profil -->
    <a class="no-underline" href="/user/{{$user->id}}" onclick="event.stopPropagation()">
        <img src="{{ asset($user->getAvatar()) }}" alt="Profile Image"
            class="w-12 h-12 rounded-full mr-4 shadow-lg">
    </a>

    <!-- Nom et date -->
    <div>
        <div class="text-gray-900 dark:text-gray-200 flex flex-row">
            <div class="flex flex-row items-center">
                <a href="/user/{{$user->id}}" onclick="event.stopPropagation()"
                    class="mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-gray-400 transition duration-150 ease-in-out">
                    {{ $user->name }}
                </a>
                @if($user->moderator)
                <div title="Modérateur vérifié">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-4 text-green-500 ml-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                @endif
            </div>

            @if ($sharedPost != null && $sharedPost->user != null)
            <span class="ml-2 self-center">a partagé un post de</span>
            <a href="/user/{{$sharedPost->user->id}}" onclick="event.stopPropagation()"
                class="mx-2 text-lg font-bold text-gray-700 dark:text-white hover:dark:text-gray-300">
                {{ $sharedPost->user->name }}
            </a>
            @else
            @auth
            @if (auth()->user()->id !== $user->id)
            <livewire:user.follow id="{{ $user->id }}" :key="$key" />
            @endif
            @endauth
            @endif
            @if ($edited)
            <span class="italic self-center ml-5">Post modifié</span>
            @endif
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400">{{ strftime('%d %B %Y à %H:%M',
            strtotime($time)) }}
        </p>
    </div>
    @auth
    @if ($displayEditButtons && auth()->user()->id == $user->id || $displayEditButtons && auth()->user()->moderator)
    <div class="ml-auto flex flex-row items-start self-start space-x-2">
        @if (auth()->user()->id == $user->id)
        <!-- Éditer -->
        <button title="Éditer le post" onclick="event.stopPropagation(); openEditPopup();"
            class="like-button flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:blue-red-500 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
            </svg>
        </button>
        @endif
        <!-- Supprimer -->
        <button title="Supprimer le post" onclick="event.stopPropagation(); openDeleteConfirmationPopup();"
            class="like-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </button>

    </div>
    @endif
    @endauth
</div>

<x-script-show-edit-post-popup />