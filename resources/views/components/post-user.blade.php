

<div {{ $attributes->merge(['class' =>"post-header flex items-center"]) }}>
    <!-- Image de profil -->
    <img src="{{ $user->avatar ?? 'path/to/default/avatar.png' }}" alt="Profile Image"
        class="w-12 h-12 rounded-full mr-4 shadow-lg">

    <!-- Nom et date -->
    <div>
        <div class="text-gray-900 dark:text-gray-200">
            <a href="/user/{{$user->id}}" class="text-lg font-bold text-gray-700 dark:text-white">
                {{ $user->name }}
            </a>

            @if ($sharedPost != null && $sharedPost->user != null)
                a partagé un post de 
                <a href="/user/{{$sharedPost->user->id}}" class="text-lg font-bold text-gray-700 dark:text-white">
                    {{ $sharedPost->user->name }}
                </a>
            @else
            @auth
                <livewire:user.follow id="{{ $user->id }}" :key="$key" />
            @endauth
            @endif
        </div>

        <p class="text-sm text-gray-600 dark:text-gray-400">{{ strftime('%d %B %Y à %H:%M',
            strtotime($time)) }}
        </p>
    </div>
</div>