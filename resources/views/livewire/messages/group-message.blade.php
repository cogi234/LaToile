<?php
    use Illuminate\Support\Carbon;
    use Livewire\Volt\Component;
    use Livewire\Attributes\Validate;
    use Livewire\Attributes\On;
    use Livewire\Attributes\Locked;
    use App\Models\User;

new class extends Component {
    #[Locked]
    public bool $enabled = false;

    #[On('open-message-creator')]
    public function open() {
       
        $this->enabled = true;
    }
    #[On('close-message-creator')]
    public function close(){
        $this->reset('enabled');
    }
}

?>

<div id="new_message_form" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-scroll">
    <div class="flex flex-row-reverse pb-2">
        <!-- Close button -->
        <button wire:click='close' title="Fermez le panneau"
             class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Publier un post</span>
    </div>
    <script>
        function showMessageCreator() {
            new CustomEvent('open-message-creator')
        }
    </script>
</div>

{{-- <div>
    <!-- Modal pour la création de groupe -->
    <div x-data="{ open: @entangle('showModal') }">
        <div x-show="open" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex justify-center items-center">
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-1/2">
                <h2 class="text-xl font-semibold mb-4 dark:text-white">Créer un groupe de messages</h2>

                <!-- Champ pour nommer le groupe -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300">Nom du groupe</label>
                    <input type="text" wire:model="groupName" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Nom du groupe" />
                </div>

                <!-- Barre de recherche pour trouver des utilisateurs -->
                <div class="mb-4">
                    <label class="block text-gray-700 dark:text-gray-300">Rechercher des utilisateurs</label>
                    <input type="text" wire:model="searchQuery" class="w-full p-2 border rounded dark:bg-gray-700 dark:text-white" placeholder="Rechercher..." />
                </div>

                <!-- Liste des utilisateurs à sélectionner -->
                <div class="mb-4 max-h-48 overflow-y-auto">
                    @foreach($users as $user)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" wire:click="toggleUserSelection({{ $user->id }})" @if(in_array($user->id, $selectedUsers)) checked @endif class="mr-2" />
                            <span class="dark:text-white">{{ $user->name }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Boutons de validation et d'annulation -->
                <div class="flex justify-end">
                    <button wire:click="createGroup" class="btn btn-primary mr-2">Créer</button>
                    <button wire:click="$emit('closeGroupModal')" class="btn btn-secondary">Annuler</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    Livewire.on('openGroupModal', () => {
        // Ouvrir le modal en utilisant AlpineJS ou toute autre méthode que vous préférez
        document.getElementById('groupModal').style.display = 'block';
    });

    Livewire.on('closeGroupModal', () => {
        // Fermer le modal
        document.getElementById('groupModal').style.display = 'none';
    });
</script>
 --}}
