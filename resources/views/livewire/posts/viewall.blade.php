<?php

use Livewire\Volt\Component;
use App\Models\Post;

new class extends Component {
    public $posts = [];
    public $moreAvailable = true;

    public function mount()
    {
            $this->posts = Post::orderby('id', 'desc')->take(10)->get();
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
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto">Charger plus de posts</x-primary-button>
    @else
        <div>Il n'y a plus de post à voir.</div>
    @endif
</div>
