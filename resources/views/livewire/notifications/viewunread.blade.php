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

    public function mount() {
        setlocale(LC_TIME, 'fr');
    }

    #[On('open-notifications-display')]
    public function loadNotifications() {
        $this->notifications = Auth::user()->unreadNotifications()->take(10)->get();
    }

    public function markRead(string $id) {
        DatabaseNotification::find($id)->markAsRead();
        $this->dispatch('change-notification-read'); 
    }

}; ?>

<div class="p-1">
    @foreach ($notifications as $notification)
    @php
        $message = $notification->data['short_message'] ?? $notification->data['message'];
        $formattedTime = strftime('%d %B %Y à %H:%M', strtotime($notification->created_at));
        $url = $notification->data['url'] ?? route('notifications');
    @endphp
    <a class="block w-full px-4 py-2 leading-5 transition duration-150 ease-in-out rounded-lg text-sm
        text-gray-700 hover:bg-gray-200 focus:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
        href="{{ $url }}" wire:mouseenter='markRead("{{ $notification->id }}")' title="{{ $notification->data['message'] }}">
        <div class="text-xs text-right dark:text-gray-400 mb-1">{{$formattedTime}}</div>
        <div>{{ $message }}</div>
    </a>
    <hr class="mx-1 border-gray-900 border-2 rounded">
    @endforeach

    <a class="block w-full px-4 py-2 leading-5 transition duration-150 ease-in-out rounded-lg text-sm
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
