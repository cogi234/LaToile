<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

new class extends Component {
    public int $tagId;
    public bool $followed;

    public function mount(int $tagId)
    {
        $this->tagId = $tagId;
        $this->updateFollowStatus();
    }

    public function updateFollowStatus(int $tagId = -1)
    {
        if(Auth::check()){
            if ($tagId == $this->tagId || $tagId == -1) {
            $this->followed = Auth::user()->followed_tags()->where('tag_id', $this->tagId)->exists();
        }
        }
        
    }

    public function unfollow()
    {
        $this->updateFollowStatus();
        if ($this->followed) {
            Auth::user()->followed_tags()->detach($this->tagId);
        }
        $this->followed = false;

        $this->dispatch('tag-follow-change', tagId: $this->tagId);
    }

    public function follow()
    {
        $this->updateFollowStatus();
        if (!$this->followed) {
            Auth::user()->followed_tags()->attach($this->tagId);
        }
        $this->followed = true;

        $this->dispatch('tag-follow-change', tagId: $this->tagId);
    }
};
?>
<div class="inline mx-2 mb-1 ">
    @if ($followed)
        <button wire:click='unfollow' onclick="event.stopPropagation()" 
                class="inline-flex items-center px-6 py-4 uppercase tracking-widest border border-transparent rounded-md font-semibold text-sm bg-gray-300 dark:bg-gray-600 text-grey-400 dark:text-gray-400 hover:bg-gray-400 dark:hover:bg-gray-700 focus:bg-gray-500 dark:focus:bg-gray-700 active:bg-gray-500 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            Tag suivi
        </button>
    @else
        <button wire:click='follow' onclick="event.stopPropagation()" 
                class="inline-flex items-center px-6 py-4  text-center uppercase tracking-widest border border-transparent rounded-md font-semibold text-sm bg-gray-600 dark:bg-gray-200 text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-300 focus:bg-gray-700 dark:focus:bg-gray-300 active:bg-gray-800 dark:active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
            Suivre Tag
        </button>
    @endif
</div>
