<?php

use Livewire\Volt\Component;
use App\Models\Post;

new class extends Component {
    public $posts = [];
    public $page = 1;
    public $hasMorePages = true;

    public function mount()
    {
        $this->loadPosts();
    }

    public function loadPosts()
    {
        $paginator = Post::orderby("created_at")->paginate(10, ['*'], 'page', $this->page);

        // Merge the new posts with the existing ones
        $this->posts = array_merge($this->posts, $paginator->items());

        // Check if there are more pages to load
        $this->hasMorePages = $paginator->hasMorePages();
    }

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->page++;
            $this->loadPosts();
        }
    }
}; ?>

<!-- Blade Template -->
<div wire:scroll="loadMore">
    @foreach ($posts as $post)
        <x-post-view :post="$post">{{ $post->title }}</x-post-view>
    @endforeach

    @if ($hasMorePages)
        <div wire:loading>Chargement de plus de posts...</div>
    @else
        <div>Il n'y a plus de post à voir.</div>
    @endif
</div>
