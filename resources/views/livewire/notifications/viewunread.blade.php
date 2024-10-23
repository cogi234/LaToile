<?php

use Livewire\Volt\Component;
use App\Notifications\BasicNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

new class extends Component {
    #[Locked]
    public $notifications = [];

    public function mount() { }

    #[On('open-notifications-display')]
    public function loadNotifications() {
        $this->notifications = Auth::user()->unreadNotifications()->take(10)->get();
    }

    public function markRead(string $id) {
        DatabaseNotification::find($id)->markAsRead();
        $this->dispatch('change-notification-read'); 
    }

}; ?>

<div>
    @foreach ($notifications as $notification)
    @php
        $message = $notification->data['short_message'] ?? $notification->data['message'];
        $url = $notification->data['url'] ?? route('notifications');
    @endphp
    <a class="w-full px-4 py-2 text-start text-sm leading-5 focus:outline-none transition duration-150 ease-in-out flex flex-row items-center
        text-gray-700 hover:bg-gray-200 focus:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
        href="{{ $url }}" wire:mouseenter='markRead("{{ $notification->id }}")' title="{{ $notification->data['message'] }}">
        {{ $message }}
    </a>
    <hr class="mx-1 border-gray-900 border-2 rounded">
    @endforeach

    <a class="w-full px-4 py-2 text-start text-sm leading-5 focus:outline-none transition duration-150 ease-in-out flex flex-row items-center
        text-gray-700 hover:bg-gray-200 focus:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
        href="{{ route('notifications') }}">
        Voir toutes les notifications...
    </a>
    
    <script>
        function loadPostDisplay() {
            //Envoyer l'event pour loader les notifications
            this.dispatchEvent(
                new Event('open-notifications-display')
            );
        }
    </script>
</div>
