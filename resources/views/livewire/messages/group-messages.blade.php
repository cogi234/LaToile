<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

new class extends Component {
    public $groupMessages = [];
    public $groups = [];
    public $selectedGroup = null;
    public ?Group $targetGroup = null;
    public ?int $targetGroupId = null;
    public string $messageContent = '';
    public $searchQuery = '';
    public $searchResults = [];

    public function mount(?int $targetGroupId)
    {
        $this->updateGroupConversations();

        if ($targetGroupId !== null) {
            $this->setSelectedGroup($this->targetGroupId);
        }
    }

    public function setSelectedGroup(int $targetGroupId) {
        if ($targetGroupId) {
            $this->targetUserId = null;
            $this->targetUser = null;
            return;
        }

        $this->targetGroup = Group::find($this->targetGroupId);
        if (!$this->targetGroup) { 
            $this->targetGroupId = null;
            $this->targetGroup = null;  
            session()->flash('error', 'Groupe non trouvé');
            return;
        }

        $this->targetGroupId = $targetGroupId;
        $this->targetUser = $targetUser;
        $this->updateSelectedConversation();
        $this->dispatch('updateSelectedConversation');
    }

    public function updateGroupConversations() {
        
        $this->groups = Group::whereHas('memberships', function ($query) {
            $query->where('user_id',  Auth::id());
        })->get();
        

        $this->updateSelectedConversation();
    }

    public function updateSelectedConversation() {
        

    }

}; ?>

<div class="grid grid-cols-2 h-full bg-white dark:bg-gray-800">
    <div class="border-r-2 h-full overflow-y-auto">
        
        <!-- Board pour changer avec messages de groupe + création -->
        <livewire:messages.messageBoard :isGroup='true' wire:key='messageBoardComponent' />
        @if($groups->isEmpty() && $targetGroupId == null)
        <div class="p-4">
            <p class="text-gray-500 dark:text-gray-300">
                Bienvenue à votre messagerie de groupes.
            </p>
        </div>
        @else
            <div>
                <!-- Search Bar -->
                <div class="p-4" >
                    <div class="relative">
                        <input type="text" name="query" id="searchBar"
                            class="block w-full pl-10 pr-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-900 dark:text-white rounded-full focus:outline-none focus:bg-white focus:text-gray-900 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                            placeholder="Rechercher des groupes"/>
                
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m2.1-6.95a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- List of Conversations -->
                <div>
                    @foreach ($groups as $group)
                        <div wire:click='setSelectedGroup({{ $group->id }})' wire:key='groupe_{{ $group->id }}'
                            class="p-4 max-h-[calc(100vh-200px)] hover:bg-gray-200 dark:hover:bg-gray-900 cursor-pointer overflow-y-auto 
                            @if ($group->id == $targetGroupId) bg-gray-100 dark:bg-gray-700 @endif">
                            <div class="flex items-center">
                                {{-- <img class="h-10 w-10 rounded-full" src="{{ $sender->getAvatar() }}" alt="Avatar de {{ $sender->name }}"/> --}}
                                <div class="ml-3 text-sm font-medium text-gray-900 dark:text-white ">
                                    {{ $group->name }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>