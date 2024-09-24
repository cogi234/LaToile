<div {{ $attributes->merge(['class' =>"post-header flex items-center"]) }}>
    <!-- Image de profil -->
    <a class="no-underline" href="/user/{{$user->id}}" onclick="event.stopPropagation()">
        <img src="{{ $user->avatar ?? '/images/no-avatar.png' }}" alt="Profile Image"
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4 text-green-500 ml-1">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                @endif
            </div>

            @if ($sharedPost != null && $sharedPost->user != null)
            a partagé un post de
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
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400">{{ strftime('%d %B %Y à %H:%M',
            strtotime($time)) }}
        </p>
    </div>
</div>