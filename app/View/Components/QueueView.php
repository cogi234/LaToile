<?php

namespace App\View\Components;

use App\Models\QueuedPost;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class QueueView extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public QueuedPost $queue
    ) { }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.queue-view');
    }
}
