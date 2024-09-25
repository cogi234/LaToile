<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;  
use Livewire\Attributes\Locked;  
use App\Models\Post;
use App\Models\User;

new class extends Component {
    #[Locked]
    public int $likeCount;
    #[Locked]
    public int $postId;
    #[Locked]
    public bool $isLiked;
    #[Locked]
    public string $formattedLikeCount;

    public function mount(int $id)
    {
        $this->postId = $id;
        $post = Post::where('id', $this->postId)->first();
        $this->likeCount = $post->likes()->count();
        $this->formattedLikeCount = $this->formatLikeCount($this->likeCount);

        $this->updateLikeStatus();
    }

    public function updateLikeStatus(int $postId = -1)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            $this->isLiked = Auth::user()->likes()->where('post_id', $this->postId)->exists();
        } else {
            $this->isLiked = false; // Handle unauthenticated users
        }
    }

    public function like()
    {
        if (Auth::check() && !$this->isLiked) {
            Auth::user()->likes()->attach($this->postId);
            $this->isLiked = true;
            $this->likeCount++;
            $this->formattedLikeCount = $this->formatLikeCount($this->likeCount);
        }
    }

    public function unlike()
    {
        if (Auth::check() && $this->isLiked) {
            Auth::user()->likes()->detach($this->postId);
            $this->isLiked = false;
            $this->likeCount--;
            $this->formattedLikeCount = $this->formatLikeCount($this->likeCount);
        }
    }

    public function toggleLike()
    {
        if ($this->isLiked) {
            $this->unlike();
        } else {
            $this->like();
        }
    }

    public function formatLikeCount($count)
    {
        if ($count >= 1000 && $count < 1000000) {
            return round($count / 1000, 1) . 'k';
        } elseif ($count >= 1000000) {
            return round($count / 1000000, 1) . 'M';
        }
        return $count;
    }
};
?>

<div>
    <button wire:click="toggleLike" title="Aimer"
        class="like-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 mr-4 dark:hover:text-red-500"
        onclick="event.stopPropagation()">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6" fill="{{$isLiked ? 'currentColor' : 'none'}}">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
        <span class="ml-1">{{ $formattedLikeCount }}</span>
    </button>
</div>
