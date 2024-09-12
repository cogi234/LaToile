<?php

use Livewire\Volt\Component;
use App\Models\Post;

new class extends Component {
    public $posts = [];
    public $moreAvailable = true;

    public function mount()
    {
            $this->posts = Post::orderby('id', 'desc')->take(10)->get();
            //User::find(1)->posts
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newPosts = Post::where('id', '<', $this->posts->last()->id)->orderby('id', 'desc')->take(10)->get();

            // Merge the new posts with the existing ones
            $this->posts = $this->posts->merge($newPosts);

            // Check if there are more pages to load
            $this->moreAvailable = $newPosts->isNotEmpty();
        }
    }
}; ?>

<!-- Blade Template -->
<div>
    @foreach ($posts as $post)
        <x-post-view :post="$post">{{ $post->title }}</x-post-view>
    @endforeach

    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de posts
        </x-primary-button>
    @else
        <div>Il n'y a plus de post à voir, revenez plus tard.</div>
    @endif
</div>
