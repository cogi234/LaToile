<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Locked;
use App\Models\User;

new class extends Component {

    #[Locked]
    public User $user;

    public function mount(User $user) 
    {
        $this->user = $user;
    }

    public function unbanUser()
    {
        $this->user->unban();

        // Envoyer l'event pour reset le contenu des tabs
        $this->dispatch('reset-post-views');
    }
};
?>

<button wire:click="unbanUser" title="Débannir l'utilisateur"
class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-500 dark:hover:blue-green-500 mr-4"
onclick="event.stopPropagation()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
    <span class="ml-1">Débannir</span>
</button>
