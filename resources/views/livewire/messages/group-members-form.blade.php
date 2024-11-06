<?php
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Group;

new class extends Component {
    public $members = [];
    public $errorMessage = '';
    public $targetGroup = null;
    public $groupMembersCount = 0;
    public $newMemberEmail = '';
    public $pageNum = 1;
    public $isCreator = false;

    public string $search = '';
    public $users = [];
    public $selectedUsers = [];
    #[Locked]
    public bool $enabled = false;

    public function mount($targetGroup) {
        $this->enabled = false;
        $this->targetGroup = $targetGroup;
        $this->isCreator = Group::find($this->targetGroup->id)
            ->memberships()
            ->where('user_id', Auth::id())
            ->where('status', 'creator')
            ->exists();
        $this->loadGroupMembers();
    }

    #[On('open-member-menu')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-member-menu')]
    public function close() {
        $this->reset('enabled', 'search', 'users', 'pageNum', 'selectedUsers');
    }

    public function loadGroupMembers() {
        // Charger les membres actuels du groupe
        $this->members = Group::find($this->targetGroup->id)->memberships()->get();
        if($this->members){
            $this->groupMembersCount = $this->members->count();
        }else {
            $this->groupMembersCount = 0;
        }
    }

    public function updatedSearch() {
        if (trim($this->search) === '') {
            $this->users = [];
        } else {
            $memberIds = $this->members ? $this->members->pluck('id')->toArray() : [];

            $invites = Group::find($this->targetGroup->id)->invites()->get();
            $inviteIds = $invites ? $invites->pluck('id')->toArray() : [];

            $excludedIds = array_merge($this->selectedUsers, $memberIds, $inviteIds);

            $this->users = User::where('name', 'like', '%' . $this->search . '%')
                ->whereNotIn('id', $excludedIds)
                ->where('id', '!=', Auth::id())
                ->take(10)
                ->get();
        }
    }

    public function selectUser($userId) {
        if (!in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
        }

        $this->users = collect($this->users)->filter(fn($user) => $user->id !== $userId);
    }

    public function deselectUser($userId) {
        $this->selectedUsers = array_values(array_filter($this->selectedUsers, fn($id) => $id !== $userId));

        $user = User::find($userId);
        if ($user) {
            $this->users[] = $user;
        }
    }

    public function addUsers() {
        if(!empty($this->selectedUsers)){
            foreach ($this->selectedUsers as $selectedUser) {
                User::find($selectedUser)->group_invites()->attach($this->targetGroup, ['status' => 'invite']);
            }
        }
        $this->close();
    }

    public function removeUser($userId) {
        if ($this->isCreator && $userId !== Auth()->id) {
            $this->targetGroup->members()->detach($userId);
            $this->loadGroupMembers();
        }
    }

    public function goToPage($pageNum) {
        $this->pageNum = $pageNum;
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
        @if ($pageNum === 1)
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Membres du groupe ({{ $groupMembersCount }})</span>

            {{-- <!-- Affichage de l'erreur -->
            @if($errorMessage)
                <div class="bg-red-500 text-white p-2 rounded mb-4" id="errorBox">
                    {{ $errorMessage }}
                </div>
            @endif --}}

            <!-- Liste des membres du groupe -->
            <ul class="mb-4 overflow-y-auto h-52">
                @foreach ($members as $member)
                    @php
                        $isMemberCreator = Group::find($this->targetGroup->id)
                        ->memberships()
                        ->where('user_id', $member->id)
                        ->where('status', 'creator')
                        ->exists();
                    @endphp
                    <li class="grid grid-cols-[1fr,3fr,1fr] justify-items-center items-center py-2 px-3 bg-gray-100 dark:bg-gray-700 rounded mb-2">
                        <span class="flex items-center text-gray-800 dark:text-gray-300">
                            <img src="{{ $member->getAvatar() }}" alt="Profile Image" class="w-16 h-16 rounded-full mr-4 shadow-lg">
                            {{ $member->name }}
                        </span>
                        <span class="text-gray-800 dark:text-gray-300">
                            @if ($isMemberCreator)
                                Créateur
                            @endif
                        </span>
                        <span>
                            <!-- Bouton de retrait visible seulement pour le créateur -->
                            @if ($isCreator && $member->id !== Auth::id())
                                <button wire:click="removeUser({{ $member->id }})" class="text-red-600 dark:text-red-400 hover:text-red-800">
                                    Retirer
                                </button>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
            @if($isCreator)
                <!-- Bouton pour ajouter un nouveau membre, visible pour tous -->
                <div>
                    <button wire:click="goToPage(2)" class="w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Ajouter un membre
                    </button>
                </div>
            @endif
        @elseif ($pageNum === 2)
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Nouveau Message</span>
            <div>
                <!-- Barre de recherche -->
                <input type="text" id="searchUserBar" placeholder="Rechercher des utilisateurs..." 
                wire:model.live="search"
                class="w-full p-2 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">

                <!-- Affichage des utilisateurs correspondants à la recherche uniquement si la recherche n'est pas vide -->
                @if (count($users) > 0)
                    <ul class="overflow-y-auto h-52">
                        @foreach ($users as $user)
                            <li wire:click="selectUser({{ $user->id }})" wire:key='userSelect_{{ $user->id }}'
                                class="flex p-2 cursor-pointer text-black dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700">
                                <div id="avatar">
                                    <img class="w-12 h-12 rounded-full mr-4 shadow-lg" alt="Profile Image"
                                        src="{{ $user->getAvatar() }}"/>
                                </div>
                                <div id="Name">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $user->name }}
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Utilisateurs sélectionnés -->
            <div class="mt-4">
                <span class="text-sm text-gray-500 dark:text-gray-400">Utilisateurs sélectionnés :</span>
                <div class="flex flex-wrap">
                    @foreach ($selectedUsers as $userId)
                        @php
                            $user = \App\Models\User::find($userId);
                        @endphp
                        <span wire:click="deselectUser({{ $userId }})" wire:key='userDeselect_{{ $userId }}'
                            class="flex items-center cursor-pointer text-black dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded m-1 outline outline-1">
                            {{ $user->name }}
                            <span class="ml-2 text-red-500 hover:text-red-700 transition-colors duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 inline-block">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </span>
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3">
                <button wire:click="addUsers" class="mt-4 w-full py-2 
                    @if(count($selectedUsers) > 0) 
                        bg-blue-500 hover:bg-blue-600 text-white
                    @else 
                        bg-gray-300 text-gray-500 cursor-not-allowed
                    @endif rounded-lg"
                    @if(count($selectedUsers) === 0) disabled @endif>
                    @if(count($selectedUsers) === 1)
                        Ajouter l'usager
                    @elseif (count($selectedUsers) >= 2)
                        Ajouter les usagers
                    @else
                        Veuillez sélectionner un usager à ajouter
                    @endif
                </button>
            </div>
        @endif
        @if($isCreator)
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
                        @else 
                            bg-gray-300
                        @endif">
                    2
                </button>
            </div>  
        @endif   
    </div>
</div>
