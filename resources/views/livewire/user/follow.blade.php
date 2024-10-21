<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; 

new class extends Component {
    public int $id;
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
        
        $this->dispatch('user-follow-change', id: $this->id);
    }

}; ?>

<div  class="inline-block mx-2 mb-1">
@if ($followed)
    <button wire:click='unfollow' title="Arrêter de suivre la personne" onclick="event.stopPropagation()" class="inline-flex items-center px-2 py-0.5 uppercase tracking-widest border border-transparent rounded-md font-semibold text-xs bg-gray-300 dark:bg-gray-600 text-grey-400 dark:text-gray-400 hover:bg-gray-400 dark:hover:bg-gray-700 focus:bg-gray-500 dark:focus:bg-gray-700 active:bg-gray-500 dark:active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>                
        Suivi
    </button>
@else
    <button wire:click='follow' title="Suivre la personne" onclick="event.stopPropagation()" class="inline-flex items-center px-2 py-0.5 uppercase tracking-widest border border-transparent rounded-md font-semibold text-xs bg-gray-600 dark:bg-gray-200 text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-gray-300 focus:bg-gray-700 dark:focus:bg-gray-300 active:bg-gray-800 dark:active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>          
        Suivre
    </button>
@endif
</div>