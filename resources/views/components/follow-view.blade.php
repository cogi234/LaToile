@php
    use App\Models\User;
    $followedUsersCount = User::find($user->id)->followed_users()->count();
    $followingUsersCount = User::find($user->id)->followers()->count();
@endphp

<div class="max-w-5xl mx-auto px-3 sm:px-8">
    <div class=" flex items-center text-lg font-semibold text-gray-800 dark:text-white pb-4">
        <span><img src="{{ $user->getAvatar() }}" alt="Profile Image"
            class="w-16 h-16 rounded-full mr-4 shadow-lg"></span>
            <span>{{$user->name}}</span>
    </div>
    <!-- Ajout des onglets -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5">
        <div class="tabs p-6 text-gray-900 dark:text-gray-100">
            <!-- Les blocs sont maintenant des liens entièrement cliquables -->
            <a href="/user-followers/{{$user->id}}" class="tab active" id="followed-tab" onclick="showContent('followed')">
                Abonnements ({{$followedUsersCount}})
            </a>
            <a href="/user-followings/{{$user->id}}" class="tab" id="following-tab" onclick="showContent('following')">
                Abonnés ({{$followingUsersCount}})
            </a>
        </div>
    </div>
    
    <!-- Contenu associé aux onglets -->
    <div class="bg-transparent overflow-hidden">
        <div class="text-gray-900 dark:text-gray-100">
            <div id="followed-content" class="content-section" style="display: block;">
                <livewire:user.view-followed-users :userId="$user->id" wire:key='followed-users'/>
            </div>
            <div id="following-content" class="content-section" style="display: none;">
                <livewire:user.view-following-users :userId="$user->id" wire:key='following-users'/>
            </div>
        </div>
    </div>
</div>
{{-- href="javascript:void(0);" --}}