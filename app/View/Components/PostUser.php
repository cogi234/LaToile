<?php

namespace App\View\Components;

use App\Models\User;
use App\Models\Post;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PostUser extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public User $user,
        public string $time,
        public int $postId,
        public array $postContent = [],
        public string $key,
        public ?Post $sharedPost = null,
        public bool $edited = false,
        public string $timeEdited = '',
        public bool $displayEditButton = true,
        public bool $displayDeleteButton = true,
    ) { }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.post-user');
    }
}
