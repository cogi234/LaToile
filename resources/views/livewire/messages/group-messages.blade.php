<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;

new class extends Component {
    public $groupMessages = [];
    public $selectedGroup = null;
    public ?Group $targetGroup = null;
    public ?int $targetGroupId = null;

    public string $messageContent = '';

    public function mount(?int $targetGroupId)
    {
        $this->updateGroupConversations();

        if ($targetGroupId !== null) {
            $this->setSelectedUser($targetGroupId);

            $this->targetGroup = Group::find($this->targetGroupId);
            if (!$this->targetUser) {   
                session()->flash('error', 'Groupe non trouvé');
                return;
            }

            
        }
    }

    public function setSelectedGroup(int $targetGroupId) {
        if ($targetGroupId == Auth::id()) {
            $this->targetUserId = null;
            $this->targetUser = null;
            return;
        }

        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            $this->targetUserId = null;
            $this->targetUser = null;
            return;
        }

        $this->targetUserId = $targetUserId;
        $this->targetUser = $targetUser;
        $this->updateSelectedConversation();
        $this->dispatch('updateSelectedConversation');
    }

}; ?>

<div>
    Groupes
</div>
