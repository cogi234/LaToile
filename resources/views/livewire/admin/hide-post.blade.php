<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;  
use App\Models\Post;

new class extends Component {

    #[Locked]
    public int $postId;

    #[Locked]
    public bool $isHidden = false;

    public function mount(int $id)
    {
        $this->postId = $id;
        $post = Post::where('id', $this->postId)->first();

        // Check if the post is hidden
        $this->isHidden = (bool) $post->hidden;
    }

    public function hide()
    {
        if (Auth::check() && !$this->isHidden) {
            $post = Post::find($this->postId);
            if ($post) {
                $post->hidden = 1; // Set hidden to true (1)
                $post->save(); // Save changes
                $this->isHidden = true; // Update the property
            }
        }
    }

    public function unHide()
    {
        if (Auth::check() && $this->isHidden) {
            $post = Post::find($this->postId);
            if ($post) {
                $post->hidden = 0; // Set hidden to false (0)
                $post->save(); // Save changes
                $this->isHidden = false; // Update the property
            }
        }
    }

    public function toggleHide()
    {
        if ($this->isHidden) {
            $this->unHide(); // Call unHide if currently hidden
        } else {
            $this->hide(); // Call hide if currently visible
        }
    }
};
?>

<div>
    <button title="Cacher ou rétablir la visibilité du post" wire:click="toggleHide"
    class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-500 mr-4" onclick="event.stopPropagation()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
    </svg>                                 
    <span class="ml-1">Status : {{ $isHidden ? 'Post Invisible' : 'Post Visible' }}</span>
</button>
</div>
