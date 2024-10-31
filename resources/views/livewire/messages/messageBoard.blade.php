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
            <div class="border-r-2 pr-2">
                <button @class(['text-blue-500' => !$isGroupConversation]) wire:click='goToPrivateMessages' title="Conversations">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </button>
            </div>
           <div class="pr-2">
                <button @class(['text-blue-500' => $isGroupConversation]) wire:click='goToGroupMessages' title="Groupes">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>                      
                </button>
           </div>
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
