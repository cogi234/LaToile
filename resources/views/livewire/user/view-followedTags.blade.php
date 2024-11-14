<?php
use Illuminate\Support\Facades\Log;
use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;

new class extends Component {
    public $followedtags = [];
    public $userId = null;

    #[Locked]
    public bool $enabled = false;

    public function mount($userId) {
        $this->enabled = false;
        $this->userId = $userId;
        $this->loadFollowedTags();
    }

    #[On('open-fTags-menu')]
    public function open() {
        $this->enabled = true;
    }

    #[On('close-fTags-menu')]
    public function close() {
        $this->reset('enabled');
    }

    public function loadFollowedTags() {
        // Charger les tags qui l'usager suit
        $this->followedtags = User::find($this->userId)->followed_tags()->get();
    }
}?>

<div id="fTags_list" class="
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
        
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Tags suivis</span>

        <!-- Liste des tags followed -->
        <ul class="mb-4 overflow-y-auto h-52">
            @foreach ($followedtags as $followedtag)
                <li class="flex justify-between mr-4 items-center py-2 px-3 bg-gray-100 dark:bg-gray-700 rounded mb-2">
                    <a href="/tag/{{ $followedtag->id }}" title="{{ $followedtag->name }}">
                        <span class="flex items-center text-gray-800 dark:text-gray-300">
                            # {{ $followedtag->name }}
                        </span>
                    </a>
                    <div class="text-6xl text-center p-1 m-1 rounded-md dark:text-gray-400"> <livewire:tags.follow :tagId="$followedtag->id" :key="$followedtag->id" />  </div>
                </li>
            @endforeach
        </ul>
    </div>
    <script>
        function toggleFollowedTagsMenu() {
            this.dispatchEvent(
                new CustomEvent('open-fTags-menu')
            );
        }
    </script>
</div>