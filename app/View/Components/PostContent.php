<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PostContent extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $content,
        public int $postId
    ) { }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.post-content');
    }
}
