<?php

    use Livewire\Volt\Component;
    use Illuminate\Support\Facades\Auth;
    use Livewire\Attributes\On;  
    use App\Models\Post;
     use App\Models\User;


    new class extends Component {
        public int $postId;
        public bool $isLiked;
        public $post;

        public function mount(int $postId)
        {
            $this->postId = $postId;
            $this->post = Post::where('id', $this->postId)->first();

            $this->updateLikeStatus();
        }

        #[On('post-like-change')]
        public function updateLikeStatus(int $postId = -1)
{
    // Check if the user is authenticated
    if (Auth::check()) {
        if ($postId == $this->postId || $postId == -1) {
            $this->isLiked = Auth::user()->likes()->where('post_id', $this->postId)->exists();
        }
    } else {
        $this->isLiked = false; // Handle unauthenticated users
    }
}

public function like()
{
    if (Auth::check() && !$this->isLiked) {
        Auth::user()->likes()->attach($this->postId);
        $this->isLiked = true;
        $this->dispatch('post-like-change', postId: $this->postId);
    }
}

public function unlike()
{
    if (Auth::check() && $this->isLiked) {
        Auth::user()->likes()->detach($this->postId);
        $this->isLiked = false;
        $this->dispatch('post-like-change', postId: $this->postId);
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
    };

    ?>

<div>
    <button wire:click="toggleLike" title="Aimer"
        class="like-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 mr-4 dark:hover:text-red-500 ">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
        </svg>
        <span class="ml-1">{{ $post->likes->count() }}</span>
    </button>
</div>