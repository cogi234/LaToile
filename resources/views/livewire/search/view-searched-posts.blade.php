<?php

use App\Models\Post;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {

    public $query;
    public $matchedPosts = [];
    public $moreAvailable = true;

    public function mount($query)
    {
        $this->query = $query;
        $this->matchedPosts = Post::where('content', 'like', '%' . $this->query . '%')
            ->where('hidden', 0)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $matchedPosts = Post::where('content', 'like', '%' . $this->query . '%')
                ->orderBy('updated_at', 'desc')
                ->get();

            // Check if there are more pages to load
            $this->moreAvailable = $matchedPosts->count() == 10;
        }
    }

    public function resetPosts(){
        // Check if there are more pages to load
        $this->moreAvailable = $this->matchedPosts->isNotEmpty();
    }
}; ?>

<div>
    @foreach ($matchedPosts as $matchedPost)
        <x-post-view :post="$matchedPost" wire:key='post_{{ $matchedPost->id }}'>{{ $matchedPost->title }}</x-post-view>
    @endforeach

    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            
            Charger plus de posts
        </x-primary-button>
    @endif
</div>
