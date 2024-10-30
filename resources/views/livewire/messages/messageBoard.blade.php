<?php
use Livewire\Volt\Component;

new class extends Component {
    public bool $isGroupConversation = false;

    public function mount(bool $isGroup = false)
    {
        $this->isGroupConversation = $isGroup;
    }
    public function goToPrivateMessages(){
        if($this->isGroupConversation){
            $this->redirect('/messages');
        }
    }
    public function goToGroupMessages(){
        if(!$this->isGroupConversation){
            $this->redirect('/messages/group');
        }
    }
};
?>
<div>
    <div class="flex flex-row justify-between items-center p-4 bg-gray-100 dark:bg-gray-700">
        <div class="flex flex-row gap-3 text-xl font-semibold dark:text-white">
            <button @class(['text-blue-500' => !$isGroupConversation]) wire:click='goToPrivateMessages'>
                <span>Conversations</span>
            </button>
            <span>/</span>
            <button @class(['text-blue-500' => $isGroupConversation]) wire:click='goToGroupMessages'>
                <span>Groupes</span>
            </button>
        </div>
        <button type="button" id="addMessages" onclick="showMessageCreator()" 
            class="btn btn-primary text-gray-500 dark:text-gray-300" title="Créer un groupe de discussion">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
        </button>
    </div>
    <!-- Messsagerie de groupe -->
    <livewire:messages.group-message-form />
</div>
