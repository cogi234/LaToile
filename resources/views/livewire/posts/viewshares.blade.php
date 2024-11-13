<?php

use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\On; 
use Livewire\Attributes\Locked;

new class extends Component {
    #[Locked]
    public $shares;
    #[Locked]
    public $moreAvailable = true;
    #[Locked]
    public $post;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->shares = $this->post->original_shares()
            ->withCount('tags as tags_count')
            ->where('tags_count', 0)
            ->where('content', '[]')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->shares->count() == 10;
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newShares = $this->post->original_shares()
                ->withCount('tags')
                ->where('tags_count', 0)
                ->where('content', '[]')
                ->where('created_at', '<', $this->shares->last()->created_at)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Merge the new shares with the existing ones
            $this->shares = $this->shares->concat($newShares);

            // Check if there are more pages to load
            $this->moreAvailable = $newShares->count() == 10;
        }
    }
    
    #[On('reset-post-views')]
    public function resetShares()
    {
        $this->shares = $this->post->original_shares()
            ->withCount('tags')
            ->where('tags_count', 0)
            ->where('content', '[]')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->shares->count() == 10;
    }
};
?>

<!-- Show more button -->
<div>
    @foreach ($shares as $share)
    <div class="flex items-center bg-white hover:bg-white/50 dark:bg-gray-800 dark:hover:dark:bg-gray-700
        overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0">
        <!-- Share icon -->
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
        class="size-8 text-green-400 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" 
            d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
        </svg>
          
        <a href="/user/{{$share->user->id}}" class="flex items-center cursor-pointer">
            <!-- Image de profil -->
            <div>
                <img src="{{ $share->user->getAvatar() }}" alt="Profile Image"
                    class="w-10 h-10 rounded-full mr-2 shadow-lg hover:outline hover:outline-2 hover:outline-black/10">
            </div>
            <span class="flex">
                <b>{{ $share->user->name }}</b> 

                @if($share->user->moderator)
                <div title="Modérateur vérifié">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4 text-green-500 mx-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                @endif
                @if($share->user->isBanned())
                <div title="Banni">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" 
                    stroke="currentColor" class="size-4 text-red-500 mx-1">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                            d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                @endif
            </span>
        </a>

        a partagé de
          
        <a href="/user/{{$share->previous->user->id}}" class="flex items-center cursor-pointer ml-2">
            <!-- Image de profil -->
            <div>
                <img src="{{ $share->previous->user->getAvatar() }}" alt="Profile Image"
                    class="w-10 h-10 rounded-full mr-2 shadow-lg hover:outline hover:outline-2 hover:outline-black/10">
            </div>
            <span class="flex">
                <b>{{ $share->previous->user->name }}</b> 

                @if($share->previous->user->moderator)
                <div title="Modérateur vérifié">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="size-4 text-green-500 mx-1">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                @endif
                @if($share->previous->user->isBanned())
                <div title="Banni">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" 
                    stroke="currentColor" class="size-4 text-red-500 mx-1">
                        <path stroke-linecap="round" stroke-linejoin="round" 
                            d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
                @endif
            </span>
        </a>
    </div>
    @endforeach


    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de partages
        </x-primary-button>
    @else
        <div class="dark:text-gray-300 text-center">Il n'y a plus de partages à voir, revenez plus tard.</div>
    @endif
</div>
