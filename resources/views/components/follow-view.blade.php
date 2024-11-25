@php
    use App\Models\User;
    // Récupérer le nombre de suivis (abonnements)
    $followingCount = User::find($user->id)->followed_users()->count();
    // Récupérer le nombre d'abonnés
    $followersCount = User::find($user->id)->followers()->count();
@endphp

<div class="max-w-5xl mx-auto px-3 sm:px-8">
    <div class="flex items-center text-lg font-semibold text-gray-800 dark:text-white pb-4">
        <a class="flex flex-row" href="/user/{{$user->id}}">
            <img src="{{ $user->getAvatar() }}" alt="Profile Image" class="w-16 h-16 rounded-full mr-4 shadow-lg">
            <span class="hover:underline text-gray-900 dark:text-gray-100">{{$user->name}}</span>
        </a>
    </div>

    <!-- Onglets -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg mb-3 md:mb-5">
        <div class="tabs p-6 text-gray-900 dark:text-gray-100">
            <!-- Onglet des abonnements (suivis) -->
            <a href="/user/{{$user->id}}/followings"
               class="tab {{ request()->is('user/' . $user->id . '/followings') ? 'active' : '' }}"
               id="following-tab">
                Abonnements : {{ $followingCount }}
            </a>
            <!-- Onglet des abonnés -->
            <a href="/user/{{$user->id}}/followers"
               class="tab {{ request()->is('user/' . $user->id . '/followers') ? 'active' : '' }}"
               id="followers-tab">
                Abonnés : {{ $followersCount }}
            </a>
        </div>
    </div>

    <!-- Contenu dynamique selon l'onglet sélectionné -->
    <div class="bg-transparent overflow-visible">
        <div class="text-gray-900 dark:text-gray-100">
            <div id="followers-content" class="content-section {{ request()->is('user/' . $user->id . '/followers') ? 'active' : '' }}">
                @if ($followersCount > 0)
                    <livewire:user.view-followers :userId="$user->id" wire:key="followersComponent"/>
                @else
                    <p>Aucun abonné pour ce profil.</p>
                @endif
            </div>

            <div id="following-content" class="content-section {{ request()->is('user/' . $user->id . '/followings') ? 'active' : '' }}">
                @if ($followingCount > 0)
                    <livewire:user.view-following :userId="$user->id" wire:key="followingComponent"/>
                @else
                    <p>Aucun abonnement pour ce profil.</p>
                @endif
            </div>
        </div>
    </div>
</div>

