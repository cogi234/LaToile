<?php

use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\On; 
use Livewire\Attributes\Locked;

new class extends Component {
    #[Locked]
    public $likes;
    #[Locked]
    public $moreAvailable = true;
    #[Locked]
    public $post;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->likes = $this->post->likes()
            ->orderByPivot('created_at', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->likes->count() == 10;
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newLikes = $this->post->likes()
                ->wherePivot('created_at', '<', $this->likes->last()->pivot->created_at)
                ->orderByPivot('created_at', 'desc')
                ->take(10)
                ->get();

            // Merge the new likes with the existing ones
            $this->likes = $this->posts->concat($newLikes);

            // Check if there are more pages to load
            $this->moreAvailable = $newLikes->count() == 10;
        }
    }
    
    #[On('reset-post-views')]
    public function resetLikes()
    {
        $this->likes = $this->post->likes()
            ->orderByPivot('created_at', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->likes->count() == 10;
    }
};
?>

<!-- Show more button -->
<div>
    @foreach ($likes as $like)
    <a href="/user/{{$like->id}}" class="flex items-center cursor-pointer bg-white hover:bg-white/50 dark:bg-gray-800 dark:hover:dark:bg-gray-700
        overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0">
        <!-- Heart icon -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" 
            class="size-8 text-red-400 mr-2">
            <path d="m11.645 20.91-.007-.003-.022-.012a15.247 15.247 0 0 1-.383-.218 25.18 25.18 0 0 1-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0 1 12 5.052 5.5 5.5 0 0 1 16.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 0 1-4.244 3.17 15.247 15.247 0 0 1-.383.219l-.022.012-.007.004-.003.001a.752.752 0 0 1-.704 0l-.003-.001Z" />
        </svg>
          
        <!-- Image de profil -->
        <div>
            <img src="{{ $like->getAvatar() }}" alt="Profile Image"
                class="w-10 h-10 rounded-full mr-2 shadow-lg hover:outline hover:outline-2 hover:outline-black/10">
        </div>
        <span class="flex">
            <b>{{ $like->name }}</b> 

            @if($like->moderator)
            <div title="Modérateur vérifié">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="size-4 text-green-500 mx-1">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
            </div>
            @endif
            @if($like->isBanned())
            <div title="Banni">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" 
                stroke="currentColor" class="size-4 text-red-500 mx-1">
                    <path stroke-linecap="round" stroke-linejoin="round" 
                        d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            @endif

            a aimé!
        </span>
    </a>
    @endforeach

    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de j'aimes
        </x-primary-button>
    @else
        <div class="dark:text-gray-300 text-center">Il n'y a plus de j'aimes à voir, revenez plus tard.</div>
    @endif
</div>
