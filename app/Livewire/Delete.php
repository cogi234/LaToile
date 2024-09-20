<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Delete extends Component
{
    public $postId;

    public function mount($postId)
    {
        $this->postId = $postId;
    }

    public function delete()
    {
        $post = Post::findOrFail($this->postId);
        $user = Auth::user();

        // Vérifie si l'utilisateur est soit le créateur du post, soit un modérateur
        if ($user->id === $post->user_id || $user->moderator) {
            $post->delete();
        }
    }

    public function render()
    {
        return view('livewire.delete-post');
    }
}
