<?php
use Livewire\Volt\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    #[Locked]
    public $blockedUsers;

    public function mount()
    {
        // Try loading blocked users only if not already set
        if (empty($this->blockedUsers)) {
            $this->loadBlockedUsers();
        }
    }

    public function loadBlockedUsers()
    {
        // Retrieve the collection of blocked users and order them
        $this->blockedUsers = Auth::user()->blocked_users()->orderBy('blocked', 'desc')->get();
    }

    public function toggleBlock($userId)
    {
        $authUser = Auth::user();

        if ($authUser->blocked_users->contains($userId)) {
            $authUser->blocked_users()->detach($userId);
        } else {
            $authUser->blocked_users()->attach($userId);
        }

        // Reload blocked users to reflect the changes
        $this->loadBlockedUsers();
    }
};

 ?>

<div>
    @if($blockedUsers->isEmpty())
    <p>No blocked users.</p>
    @else
    <ul>
        @foreach($blockedUsers as $blockedUser)
        <div class="max-w-5xl mx-auto px-3 sm:px-8 mt-10 2xl:mt-0">
            <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full">
                <div class="flex sm:items-center items-start sm:flex-row flex-col sm:pt-0 pt-4">
                    <!-- Profile Image -->
                    <div class="flex flex-row">
                        <img src="{{ $blockedUser->getAvatar() }}" alt="Profile Image"
                            class="w-20 h-20 rounded-full mr-4 shadow-lg">

                        {{-- Name and Followers / Following --}}
                        <div class="mr-2">
                            <div class="flex flex-row">
                                <h2 class="text-xl font-semibold text-black dark:text-gray-100 mr-2">{{ $blockedUser->name }}</h2>
                            </div>
                            <p class="text-black dark:text-gray-100">Followers: {{ $blockedUser->followers()->count() }}</p>
                            <p class="text-black dark:text-gray-100">Following: {{ $blockedUser->followed_users()->count() }}</p>
                        </div>
                    </div>
                </div>
            </br>
                <div>
                    <span>  
                        <x-primary-button wire:click="toggleBlock({{ $blockedUser->id }})" class="btn {{ Auth::user()->blocked_users->contains($blockedUser->id) ? 'btn-danger' : 'btn-warning' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg> 
                            {{ Auth::user()->blocked_users->contains($blockedUser->id) ? 'Débloquer utilisateur' : ' Bloquer utilisateur' }}
                        </x-primary-button>  
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </ul>
    @endif
</div>
