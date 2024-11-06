<?php
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Group;

new class extends Component {
    public $users = [];
    public $errorMessage = '';
    public $targetGroup = null;
    public $groupMembersCount = 0;
    public $newMemberEmail = '';
    public $pageNum = 1;

    #[Locked]
    public bool $enabled = false;

    public function mount($targetGroup) {
        $this->enabled = false;
        $this->targetGroup = $targetGroup;
        $this->loadGroupMembers();
    }

    #[On('open-member-menu')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-member-menu')]
    public function close() {
        $this->reset('enabled');
    }

    public function loadGroupMembers() {
        // Charger les membres actuels du groupe
        $this->users = Group::find($this->targetGroup->id)->memberships()->get();
        $this->groupMembersCount = $this->users->count();
    }

    public function addUser() {
        // $user = User::where('email', $this->newMemberEmail)->first();

        // if ($user) {
        //     if (!$this->targetGroup->users->contains($user->id)) {
        //         $this->targetGroup->users()->attach($user->id);
        //         $this->loadGroupMembers();
        //         $this->newMemberEmail = '';
        //         $this->errorMessage = '';
        //     } else {
        //         $this->errorMessage = "Cet utilisateur est déjà dans le groupe.";
        //     }
        // } else {
        //     $this->errorMessage = "Utilisateur non trouvé.";
        // }
    }

    public function removeUser($userId) {
        // $this->targetGroup->users()->detach($userId);
        // $this->loadGroupMembers();
    }

    public function goToPage($pageNum) {
        $this->pageNum = $pageNum;
        $this->errorMessage = '';
    }
};
?>

<div id="member_list" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-scroll">
    <div class="relative z-50 top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <!-- Commande pour fermer -->
        <div class="flex flex-row-reverse pb-2">
            <button wire:click='close' title="Fermez le panneau"
                 class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Titre avec nombre de membres -->
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Membres du groupe ({{ $groupMembersCount }})</span>

        <!-- Affichage de l'erreur -->
        @if($errorMessage)
            <div class="bg-red-500 text-white p-2 rounded mb-4" id="errorBox">
                {{ $errorMessage }}
            </div>
        @endif

        <!-- Liste des membres du groupe -->
        <ul class="mb-4">
            @foreach ($users as $user)
                <li class="flex justify-between items-center py-2 px-3 bg-gray-100 dark:bg-gray-700 rounded mb-2">
                    <span class="flex items-center text-gray-800 dark:text-gray-300">
                        <span><img src="{{ $user->getAvatar() }}" alt="Profile Image"
                        class="w-16 h-16 rounded-full mr-4 shadow-lg"></span> 
                        {{ $user->name }}
                    </span>
                    <button wire:click="removeUser({{ $user->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800">
                        Retirer
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Bouton pour ajouter un nouveau membre -->
        <div>
            <button wire:click="addUser" class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Ajouter un membre
            </button>
        </div>
        
        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            <button wire:click="goToPage(1)" 
                    class="px-3 py-1 rounded-full 
                    @if($pageNum === 1) 
                        bg-gray-500 
                    @else 
                        bg-gray-300 
                    @endif">
                1
            </button>
            <button wire:click="goToPage(2)" 
                    class="ml-2 px-3 py-1 rounded-full 
                    @if($pageNum === 2) 
                        bg-gray-500 
                    @elseif(count($selectedUsers) > 0) 
                        bg-gray-300 hover:bg-gray-400 
                    @else 
                        bg-gray-300 text-gray-500 cursor-not-allowed 
                    @endif">
                2
            </button>
        </div>     
    </div>
</div>
