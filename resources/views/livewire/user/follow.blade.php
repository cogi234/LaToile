<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Notifications\UserFollowed;
use App\Models\User;

new class extends Component {
    #[Locked]
    public int $id;
    #[Locked]
    public bool $followed;

    public function mount(int $id)
    {
        $this->id = $id;
        $this->updateFollowStatus();
    }

    #[On('user-follow-change')]
    public function updateFollowStatus(int $id = -1)
    {
        if ($id == $this->id || $id == -1)
            $this->followed = Auth::user()->followed_users()->where('id', $this->id)->exists();
    }

    public function unfollow()
    {
        $this->updateFollowStatus();
        if ($this->followed)
            Auth::user()->followed_users()->detach($this->id);
        $this->followed = false;

        $this->dispatch('user-follow-change', id: $this->id);
    }

    public function follow()
    {
        $this->updateFollowStatus();
        if (!$this->followed)
            Auth::user()->followed_users()->attach($this->id);
        $this->followed = true;

        //Send a notification to the followed user
        User::find($this->id)->notify(new UserFollowed(Auth::user()));
        
        $this->dispatch('user-follow-change', id: $this->id);
    }

}; ?>

<div  class="inline-block mx-2 mb-1">
@if ($followed)
    <button wire:click='unfollow' onclick="event.stopPropagation()" class="inline-flex items-center px-2 py-0.5 uppercase tracking-widest border border-transparent rounded-md font-semibold text-xs bg-gray-300 dark:bg-gray-600 text-grey-400 dark:text-gray-400 hover:bg-gray-400 dark:hover:bg-gray-700 focus:bg-gray-500 dark:focus:bg-gray-700 active:bg-gray-500 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Suivi</button>
@else
    <button wire:click='follow' onclick="event.stopPropagation()" class="inline-flex items-center px-2 py-0.5 uppercase tracking-widest border border-transparent rounded-md font-semibold text-xs bg-gray-600 dark:bg-gray-200 text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-300 focus:bg-gray-700 dark:focus:bg-gray-300 active:bg-gray-800 dark:active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">Suivre</button>
@endif
</div>