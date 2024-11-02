<?php
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Group;

new class extends Component {
    public $groupName = '';
    public $errorMessage = '';

    #[Locked]
    public bool $enabled = false;

    public function mount() {
        $this->enabled = false;
    }

    #[On('open-groupName-menu')]
    public function open() {
        $this->enabled = true;
    }

    #[On('open-groupName-menu')]
    public function close() {
        $this->reset('enabled');
    }

    public function addUsers() {
        
    }


}
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
        
    </div>
    <script defer>
        function toggleGroupNameMenu() {
            this.dispatchEvent(
                new CustomEvent('open-groupName-menu')
            );
        }
    </script>
</div>

