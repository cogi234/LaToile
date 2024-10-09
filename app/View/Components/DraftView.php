<?php

namespace App\View\Components;

use App\Models\Draft;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DraftView extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Draft $draft
    ) { }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.draft-view');
    }
}
