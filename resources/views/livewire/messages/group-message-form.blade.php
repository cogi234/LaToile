<?php
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Group;
use App\Notifications\GroupInvitation;

new class extends Component {
    public string $search = '';
    public $users = [];
    public $selectedUsers = [];
    public $pageNum = 1;
    public string $groupName = '';
    public $errorMessage = '';

    #[Locked]
    public bool $enabled = false;

    public function mount() {
        $this->enabled = false;
    }

    #[On('open-message-creator')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-message-creator')]
    public function close() {
        $this->reset('enabled', 'search', 'users', 'selectedUsers', 'groupName', 'errorMessage', 'pageNum');
    }

    public function updatedSearch() {
        if (trim($this->search) === '') {
            $this->users = [];
        } else {
            $this->users = User::where('name', 'like', '%' . $this->search . '%')
                ->whereNotIn('id', $this->selectedUsers)
                ->where('id', '!=', Auth::id())
                ->take(config('app.posts_per_load', 20))
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

    public function createGroup() {
        if (!empty($this->groupName) && !empty($this->selectedUsers)) {
            $group = Group::create([
                'name' => $this->groupName
            ]);
            User::find(Auth::id())->group_memberships()->attach($group, ['status' => 'creator']);
            foreach ($this->selectedUsers as $selectedUser) {
                $selectedUserModel = User::find($selectedUser);

                // Vérification des permissions d'invitation
                if (!$selectedUserModel->can_get_group_invitation_from_anyone && 
                    $selectedUserModel->followed_users()->where('id', Auth::id())->count() === 0) {
                    // Afficher un message d'erreur et ne pas envoyer d'invitation
                    session()->flash('info', 'L\'utilisateur ' . $selectedUserModel->name . ' n\'accepte pas les invitations des personnes qu\'il ne suit pas. Il ne sera donc pas invité au groupe "' . $this->groupName . '".');
                    continue; // Passer à l'utilisateur suivant
                }

                // Vérification si l'utilisateur est bloqué ou s'il bloque l'auteur
                if (Auth::user()->blocked_users()->where('id', $selectedUserModel->id)->exists() || 
                    Auth::user()->blockers()->where('id', $selectedUserModel->id)->exists()) {
                    session()->flash('info', 'L\'utilisateur ' . $selectedUserModel->name . ' vous as bloqué, il ne sera donc pas invité au groupe "' . $this->groupName . '".');
                    continue; // Passer à l'utilisateur suivant
                }

                $selectedUserModel->group_invites()->attach($group, ['status' => 'invite']);
                $selectedUserModel->notify(new GroupInvitation(Auth::user(), $group));
            }

            $this->groupName = '';

            session()->flash('message', 'Le groupe a été créé avec succès.');
            $this->redirect('/messages/group');
        } else {
            session()->flash('error', 'Le nom du groupe ne peut pas être vide.');
        }
    }

    public function goToPage($pageNum) {
        if (empty($this->selectedUsers)) {
            $this->errorMessage = 'Vous devez sélectionner au moins un utilisateur.';
        } else {
            $this->pageNum = $pageNum;
            $this->errorMessage = '';
        }
    }
}
?>

<div id="new_message_form" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-scroll">
    <div class="relative z-50 top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <!-- Top command bar -->
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button wire:click='close' title="Fermez le panneau"
                 class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        @if($pageNum === 1)
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Nouvelle Conversation</span>
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
                @if(count($selectedUsers) == 1 && isset($selectedUsers[0]))
                <a href="{{ url('messages/user/' . $selectedUsers[0]) }}" class="w-full">
                    <button class="mt-4 w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Commencer une discussion
                    </button>
                </a>
                @endif
                <button wire:click="goToPage(2)" class="mt-4 w-full py-2 
                    @if(count($selectedUsers) > 0) 
                        bg-blue-500 hover:bg-blue-600 text-white
                    @else 
                        bg-gray-300 text-gray-500 cursor-not-allowed
                    @endif rounded-lg"
                    @if(count($selectedUsers) === 0) disabled @endif>
                    Créer un groupe
                </button>
            </div>
        @elseif ($pageNum === 2)
            <!-- Champ pour nommer le groupe si plus d'un utilisateur est sélectionné -->
            @if(count($selectedUsers) >= 1)
                <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Créer un groupe</span>
                <div class="mt-4">
                    <input type="text" wire:model.live="groupName" placeholder="Nom du groupe..." 
                    class="w-full p-2 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Utilisateurs sélectionnés :</span>
                    <div class="flex flex-wrap">
                        @foreach ($selectedUsers as $userId)
                            @php
                                $user = \App\Models\User::find($userId);
                            @endphp
                            <span wire:key='selectedUser_{{ $user->id }}' class="flex items-center cursor-pointer text-black dark:text-white p-2 rounded m-1 outline outline-1">
                                {{ $user->name }}
                            </span>
                        @endforeach
                    </div>
                
                    <!-- Bouton de création de groupe, désactivé si le nom du groupe est vide -->
                    <button wire:click="createGroup" 
                            class="mt-4 w-full py-2 
                            @if(count($selectedUsers) > 0 && !empty($groupName)) 
                                bg-blue-500 hover:bg-blue-600 text-white 
                            @else 
                                bg-gray-300 text-gray-500 cursor-not-allowed 
                            @endif rounded-lg"
                            @if(count($selectedUsers) === 0 || empty($groupName)) disabled @endif>
                        Créer un groupe
                    </button>
                </div>
                
            @else
                <div class="mt-4">
                    <p class="text-red-500">Veuillez choisir des usager pour créer un groupe</p>
                </div>
            @endif
        @endif

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
                    @endif"
                    @if(count($selectedUsers) === 0) disabled @endif>
                2
            </button>
        </div>        
    </div>
    <script>
        function showMessageCreator() {
            this.dispatchEvent(
                new CustomEvent('open-message-creator')
            );
        }
    </script>
</div>

