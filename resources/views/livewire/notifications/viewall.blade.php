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
    #[Locked]
    public $moreAvailable = true;

    public function mount() {
        $this->notifications = Auth::user()->notifications()
            ->orderby('id', 'desc')
            ->take(10)
            ->get();
            
        //All notifications we display in this page are marked as read
        $this->markRead($this->notifications);

        // Check if there are more pages to load
        $this->moreAvailable = $this->notifications->count() == 10;
    }

    public function loadMore() {
        if ($this->moreAvailable) {
            $newNotifs = Auth::user()->notifications()
                ->where('id', '<', $this->notifications->last()->id)
                ->orderby('id', 'desc')
                ->take(10)
                ->get();

            //All notifications we display in this page are marked as read
            $this->markRead($newNotifs);

            // Merge the new notifications with the existing ones
            $this->notifications = $this->posts->concat($newNotifs);

            // Check if there are more pages to load
            $this->moreAvailable = $newNotifs->count() == 10;
        }
    }

    public function markRead($notifs) {
        foreach ($notifs as $notif) {
            $notif->markAsRead();
        }
        $this->dispatch('change-notification-read'); 
    }

}; ?>

<div>
    @foreach ($notifications as $notification)
    @php
        $message = $notification->data['short_message'] ?? $notification->data['message'];
        $url = $notification->data['url'] ?? route('notifications');
    @endphp
    <a class="w-full px-4 py-2 text-start text-sm leading-5 my-1 focus:outline-none transition duration-150 ease-in-out flex flex-row items-center rounded-lg
        text-gray-700 hover:bg-gray-200 focus:bg-gray-200 dark:text-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:bg-gray-800"
        href="{{ $url }}" title="{{ $notification->data['message'] }}">
        {{ $message }}
    </a>
    <hr class="mx-1 border-gray-900 border-2 rounded">
    @endforeach

    
    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de notifications
        </x-primary-button>
    @else
        <div class="dark:text-gray-300 text-center">Il n'y a plus de notifications à voir, revenez plus tard.</div>
    @endif
</div>
