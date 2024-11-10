<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Post;

class ViewSpecificUser extends Component
{
    public $posts;
    public $moreAvailable = true;
    public $userId;

    public function mount($id)
    {
        $this->userId = $id;

        $this->posts = Post::blockedUserPostCheck()->where('user_id', $this->userId)
            ->orderby('id', 'desc')->take(10)->with('user')->get();

        // Vérifie s'il y a plus de posts à charger
        $this->moreAvailable = $this->posts->isNotEmpty();
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newPosts = Post::blockedUserPostCheck()->where('user_id', $this->userId)
                ->where('id', '<', $this->posts->last()->id)
                ->orderby('id', 'desc')->take(10)->with('user')->get();

            // Fusionne les nouveaux posts avec les existants
            $this->posts = $this->posts->concat($newPosts);

            // Vérifie s'il y a plus de posts à charger
            $this->moreAvailable = $newPosts->count() == 10;
        }
    }

    public function render()
    {
        return view('livewire.view-specific-user');
    }
}

