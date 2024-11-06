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
        $this->users = Group:: ->get();
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
                    <span class="text-gray-800 dark:text-gray-300">{{ $user->name }} ({{ $user->email }})</span>
                    <button wire:click="removeUser({{ $user->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800">
                        Retirer
                    </button>
                </li>
            @endforeach
        </ul>

        <!-- Formulaire pour ajouter un nouveau membre -->
        <div>
            <input type="email" wire:model="newMemberEmail" placeholder="Email du membre" class="w-full p-2 mb-2 border border-gray-300 rounded">
            <button wire:click="addUser" class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Ajouter un membre
            </button>
        </div>
    </div>
</div>
