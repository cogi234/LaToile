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

<div title="Voir les notifications" class="w-10 h-10 rounded-full shadow-lg" wire:poll='loadNotificationsCount'
    style="background-image:url('{{auth()->user()->getAvatar()}}'); background-position: center; background-size: cover;">
    @if ($count >= 99)
    <div class="flex flex-row">
        <svg xmlns="http://www.w3.org/2000/svg" title="Voir les notifications" viewBox="0 0 24 24" fill="currentColor" class="white size-7 translate-y-6 translate-x-[0.38em]">
            <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" />
            <text x="12" y="13" font-size="7" text-anchor="middle" fill="white" font-weight="bold">99+</text>
        </svg>  
    </div>
    @elseif ($count > 0)
        <svg xmlns="http://www.w3.org/2000/svg" title="Voir les notifications" viewBox="0 0 24 24" fill="currentColor" class="white size-7 translate-y-6 translate-x-[0.38em]">
            <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" />
            <text x="12" y="13" font-size="9" text-anchor="middle" fill="white" font-weight="bold">{{ $count }}</text>
        </svg>  
    @else
        <svg xmlns="http://www.w3.org/2000/svg" title="Voir les notifications" viewBox="0 0 24 24" fill="currentColor" class="white size-7 translate-y-6 translate-x-[0.38em]">
            <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0 1 13.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 0 1-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 1 1-7.48 0 24.585 24.585 0 0 1-4.831-1.244.75.75 0 0 1-.298-1.205A8.217 8.217 0 0 0 5.25 9.75V9Zm4.502 8.9a2.25 2.25 0 1 0 4.496 0 25.057 25.057 0 0 1-4.496 0Z" clip-rule="evenodd" />
        </svg> 
    @endif
</div>
