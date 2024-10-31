<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Ban;

new class extends Component {

    #[Locked]
    public int $userId = -1;

    #[Locked]
    public string $reportType = '';

    public function unbanUser(int $userId, string $reportType)
    {
        // Trouver le ban de l'utilisateur
        $ban = Ban::where('user_id', $userId)->first();
        
        if ($ban) {
            $ban->delete(); // Supprime l'enregistrement
        }

        if ($reportType === 'Report') {
            return redirect()->route('adminPage');
        } else {
            return redirect()->route('adminPageMessage');
        }
    }
};
?>

<button wire:click="unbanUser({{$userId}}, '{{$reportType}}')" title="Débannir l'utilisateur"
class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-500 dark:hover:blue-green-500 mr-4"
onclick="event.stopPropagation()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
    </svg>
    <span class="ml-1">Débannir</span>
</button>
