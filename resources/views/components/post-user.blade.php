

<div {{ $attributes->merge(['class' =>"post-header flex items-center"]) }}>
    <!-- Image de profil -->
    <img src="{{ $user->avatar ?? 'path/to/default/avatar.png' }}" alt="Profile Image"
        class="w-12 h-12 rounded-full mr-4 shadow-lg">

    <!-- Nom et date -->
    <div>
        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            {{ $user->name }}
            @auth
                <livewire:user.follow id="{{ $user->id }}" :key="$key" />
            @endauth
            @if ($sharedPost != null && $sharedPost->user != null)
                a partagé {{ $sharedPost->user->name }}
            @endif
        </h4>
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ strftime('%d %B %Y à %H:%M',
            strtotime($time)) }}</p>
    </div>
</div>