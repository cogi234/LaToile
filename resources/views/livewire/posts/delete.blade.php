<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Post;

class PostDelete extends Component
{
    public $postId;
    public $confirming = false;

    protected $rules = [
        'postId' => 'required|exists:posts,id',
    ];

    public function mount($postId)
    {
        $this->postId = $postId;
    }

    public function delete()
    {
        $this->validate();
        $post = Post::find($this->postId);
        $post->delete();
        session()->flash('message', 'Post deleted successfully.');
        return redirect()->route('posts.index'); // or wherever you want to redirect
    }

    public function render()
    {
        return view('livewire.post-delete');
    }
}
