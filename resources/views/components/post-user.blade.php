

<div class="post-header flex items-center">
    <!-- Image de profil -->
    <img src="{{ $user->avatar ?? 'path/to/default/avatar.png' }}" alt="Profile Image" class="w-12 h-12 rounded-full mr-4 shadow-lg">

    <!-- Nom et date -->
    <div>
        <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">
            {{ $user->name }}
            @auth
                <livewire:user.follow id="{{ $post->user->id }}" />
            @endauth
        </h4>
    </div>
    <hr/>
</div>