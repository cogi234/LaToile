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
        $this->matchedPosts = Post::blockedUserPostCheck()->where('content', 'like', '%' . $this->query . '%')
            ->where('hidden', false)
            ->orderBy('updated_at', 'desc')
            ->get();
    }
}; ?>

<div>
    @foreach ($matchedPosts as $matchedPost)
        <x-post-view :post="$matchedPost" wire:key='post_{{ $matchedPost->id }}'>{{ $matchedPost->title }}</x-post-view>
    @endforeach
</div>
