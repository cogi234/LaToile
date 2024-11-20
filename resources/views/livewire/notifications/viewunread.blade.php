<?php

use Livewire\Volt\Component;
use App\Notifications\BasicNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Astrotomic\Twemoji\Twemoji;

new class extends Component {
    #[Locked]
    public $notifications = [];

    public function mount() {
        setlocale(LC_TIME, 'fr');
    }

    #[On('open-notifications-display')]
    public function loadNotifications() {
        $this->notifications = Auth::user()->unreadNotifications()
            ->take(config('app.posts_per_load', 20))
            ->get();
        $this->markRead($this->notifications);
    }

    public function markRead($notifs) {
        foreach ($notifs as $notif) {
            $notif->markAsRead();
        }
        $this->dispatch('change-notification-read'); 
    }

}; ?>

<div class="p-1">
    @foreach ($notifications as $notification)
    @php
        $message = $notification->data['short_message'] ?? $notification->data['message'];
        $messageWithTwemoji = Twemoji::text($message)->svg()->toHTML();
        $formattedTime = strftime('%d %B %Y à %H:%M', strtotime($notification->created_at));
        $url = $notification->data['url'] ?? route('notifications');
    @endphp
    <a class="block w-full px-4 py-2 leading-5 transition duration-150 ease-in-out rounded-lg text-sm
        text-gray-700 hover:bg-gray-200 focus:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800 dark:focus:bg-gray-800"
        href="{{ $url }}" title="{{ $notification->data['message'] }}">
        <div class="text-xs text-right dark:text-gray-400 mb-1">{{$formattedTime}}</div>
        <div>{!! $messageWithTwemoji !!}</div>
    </a>
    <hr class="mx-1 border-gray-200 border-2 rounded">
    @endforeach

    <a class="fblock w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-800 transition duration-150 ease-in-out flex flex-row items-center"
        href="{{ route('notifications') }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
        </svg>          
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
