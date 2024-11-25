<?php
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Group;

new class extends Component {
    public $invites = [];
    public $targetGroup = null;
    public $groupInvitesCount = 0;

    #[Locked]
    public bool $enabled = false;

    public function mount($targetGroup) {
        $this->enabled = false;
        $this->targetGroup = $targetGroup;
        $this->loadGroupInvites();
    }

    #[On('open-invite-menu')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-invite-menu')]
    public function close() {
        $this->reset('enabled');
    }

    public function loadGroupInvites() {
        // Charger les invitations actuels au groupe
        $this->invites = Group::find($this->targetGroup->id)->invites()->get();
        if($this->invites){
            $this->groupInvitesCount = $this->invites->count();
        }else {
            $this->groupInvitesCount = 0;
        }
    }
}?>

<div id="member_list" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-scroll">
    {{-- <div class="relative z-50 top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <!-- Commande pour fermer -->
        <div class="flex flex-row-reverse pb-2">
            <button wire:click='close' title="Fermez le panneau"
                 class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Invitations au groupe ({{ $groupInvitesCount }})</span>

        <!-- Liste des invitations au groupe -->
        <ul class="mb-4 overflow-y-auto h-52">
            @foreach ($invites as $invite)
                <li class="grid grid-cols-[1fr,3fr,1fr] justify-items-center items-center py-2 px-3 bg-gray-100 dark:bg-gray-700 rounded mb-2">
                    <span class="flex items-center text-gray-800 dark:text-gray-300">
                        <img src="{{ $invite->getAvatar() }}" alt="Profile Image" class="w-16 h-16 rounded-full mr-4 shadow-lg">
                        {{ $invite->name }}
                    </span>
                </li>
            @endforeach
        </ul>
    </div> --}}
    <div class="relative z-50 top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
        <!-- Commande pour fermer -->
        <div class="flex flex-row-reverse pb-2">
            <button wire:click='close' title="Fermer le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500 transition duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    
        <span class="text-xl pb-2 font-semibold text-gray-800 dark:text-white mb-4">Invitations au groupe ({{ $groupInvitesCount }})</span>
    
        <!-- Liste des invitations au groupe -->
        <ul class="space-y-4 overflow-y-auto max-h-52 pr-3">
            @if($groupInvitesCount > 0)
                @foreach ($invites as $invite)
                    <li class="flex items-center justify-between bg-gray-100 dark:bg-gray-700 rounded-lg px-4 py-3 shadow-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition ease-in-out duration-150">
                        <!-- Avatar et nom de l'invité -->
                        <div class="flex items-center space-x-4">
                            <img src="{{ $invite->getAvatar() }}" alt="Profile Image" class="w-16 h-16 rounded-full shadow-lg">
                            <span class="text-lg font-medium text-gray-900 dark:text-white">{{ $invite->name }}</span>
                        </div>
    
                        <!-- Statut de l'invitation -->
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            En attente
                        </span>
                    </li>
                @endforeach
            @else
                <li class="flex justify-center items-center py-3 px-4 bg-gray-100 dark:bg-gray-700 rounded-lg text-gray-600 dark:text-gray-400">
                    Aucune invitation en cours.
                </li>
            @endif
        </ul>
    </div>    
</div>