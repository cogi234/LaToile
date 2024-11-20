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
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-auto z-50">
    <div class="relative top-1/4 w-full md:w-2/4 p-6 mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg">
        <!-- Bouton pour fermer -->
        <div class="flex justify-between items-center pb-4">
            <span class="text-2xl font-bold text-black dark:text-white">Tags suivis</span>
            <button wire:click='close' title="Fermez le panneau"
                class="text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Barre de recherche -->
        <div class="relative mb-4">
            <input type="text" id="searchTags" placeholder="Rechercher un tag..." 
                class="w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
            <svg class="absolute right-4 top-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m3-7a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <!-- Liste des tags -->
        <ul class="space-y-3 overflow-y-auto max-h-64" id="tagList">
            @foreach ($followedtags as $followedtag)
                <li class="flex justify-between items-center py-2 px-4 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-md">
                    <a href="/tag/{{ $followedtag->id }}" title="{{ $followedtag->name }}" class="flex items-center text-gray-800 dark:text-gray-300 hover:text-indigo-500 dark:hover:text-indigo-400">
                        <span>#{{ $followedtag->name }}</span>
                    </a>
                    <livewire:tags.follow :tagId="$followedtag->id" :key="$followedtag->id" />
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
    
        document.getElementById('searchTags').addEventListener('input', function () {
            const query = this.value.toLowerCase();
            const tagList = document.getElementById('tagList');
            const tags = tagList.querySelectorAll('li');
    
            tags.forEach(tag => {
                const tagName = tag.querySelector('a span').textContent.toLowerCase();
                if (tagName.includes(query)) {
                    tag.style.display = 'flex';
                } else {
                    tag.style.display = 'none';
                }
            });
        });
    
    </script>
</div>