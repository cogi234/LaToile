<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Post;

use function Laravel\Prompts\alert;

class HidePost extends Component
{
    public function render()
    {
        return view('livewire.admin.hide-post');
    }
}
