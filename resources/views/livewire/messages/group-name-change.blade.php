<?php
use Illuminate\Support\Carbon;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Group;

new class extends Component {
    public $groupName = '';
    public $errorMessage = '';
    public $targetGroup = null;

    #[Locked]
    public bool $enabled = false;

    public function mount($targetGroup) {
        $this->enabled = false;
        $this->$targetGroup = $targetGroup;
        $this->groupName = $targetGroup->name;
    }

    #[On('open-groupName-menu')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-groupName-menu')]
    public function close() {
        $this->reset('enabled');
    }

    public function changeName() {
        if ($this->groupName == null || strlen(trim($this->groupName)) <= 0 || strlen(trim($this->groupName)) > 50) {
            $this->addError('groupNameLength', 'Votre nom de groupe est trop court ou trop long. (1-50)');
            return;
        }

        $group = Group::find($this->targetGroup->id);
        $group->name = trim($this->groupName);
        $group->save();
        $this->close();
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

        <!-- Affichage de l'erreur -->
        {{-- @if($errorMessage)
            <div class="bg-red-500 text-white p-2 rounded mb-4" id="errorBox">
                {{ $errorMessage }}
            </div>
        @endif --}}
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Changer le nom du groupe</span>
        <div>
            <input wire:model='groupName' type="text" id="newNameBar" placeholder="{{$targetGroup->name}}" 
                class="w-full p-2 mb-4 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <button wire:click='changeName' class="mt-4 w-full py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
            Changer de nom
        </button>
    </div>
</div>

