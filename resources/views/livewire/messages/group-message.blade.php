<?php
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;

new class extends Component {
    public string $search = '';
    public $users = [];
    public $selectedUsers = [];

    #[Locked]
    public bool $enabled = false;

    #[On('open-message-creator')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-message-creator')]
    public function close() {
        $this->reset('enabled', 'search', 'users', 'selectedUsers');
    }

    public function updatedSearch() {
        if (trim($this->search) === '') {
            $this->users = [];
        } else {
            $this->users = User::where('name', 'like', '%' . $this->search . '%')
                ->whereNotIn('id', $this->selectedUsers)
                ->take(10)
                ->get();
        }
    }

    public function selectUser($userId) {
        if (!in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
        }

        $this->users = $this->users->filter(fn($user) => $user->id !== $userId);
    }

    public function deselectUser($userId) {
        $this->selectedUsers = array_values(array_filter($this->selectedUsers, fn($id) => $id !== $userId));

        $user = User::find($userId);
        if ($user) {
            $this->users[] = $user;
        }
    }

    // Fonction pour créer un groupe
    public function createGroup() {
        // Logique pour créer le groupe de discussion
        // Enregistrer les utilisateurs sélectionnés dans une conversation de groupe
    }
}
?>

<div id="new_message_form" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-scroll">
    <div class="relative top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
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
                        <li wire:click="selectUser({{ $user->id }})"
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
                    <span wire:click="deselectUser({{ $userId }})" class="flex items-center cursor-pointer text-black dark:text-white hover:bg-gray-200 dark:hover:bg-gray-700 p-2 rounded m-1 outline outline-1">
                        {{ $user->name }}
                        <span class="ml-2 text-red-500 hover:text-red-700 transition-colors duration-200">
                            <!-- Icône SVG pour le désélectionnement -->
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
            <a href="{{ url('messages/' . Auth::id() . '-' . $selectedUsers[0]) }}">
                <button  class="mt-4 w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Commencer une discussion
                </button>
            </a>
            @endif
            <!-- Bouton pour créer un groupe -->
            <button wire:click="createGroup" class="mt-4 w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Créer un groupe
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
