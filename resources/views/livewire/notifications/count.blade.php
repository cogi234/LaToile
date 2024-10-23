<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $count = 0;


    public function mount()
    {
        $this->loadNotificationsCount();
    }

    #[On('change-notification-read')]
    public function loadNotificationsCount() {
        $this->count = Auth::user()->unreadNotifications()->count();
    }
}; ?>

<div class="w-10 h-10 rounded-full shadow-lg" wire:poll='loadNotificationsCount'
    style="background-image:url('{{auth()->user()->getAvatar()}}'); background-position: center; background-size: cover;">

    @if ($count >= 99)
    <span class="relative bg-blue-400 text-black text-xs font-extrabold p-0.5 rounded-full top-7 border border-blue-500">99+</span>
    @elseif ($count > 0)
    <span class="relative bg-blue-400 text-black text-xs font-extrabold p-0.5 rounded-full top-7 border border-blue-500">{{$count}}</span>
    @endif
</div>
