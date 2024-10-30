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
            $query->where('user_id', Auth()->id);
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
                Bienvenue à votre messagerie. Écrivez entre vous et les autres sur La Toile
            </p>
        </div>
        @else
    </div>
</div>