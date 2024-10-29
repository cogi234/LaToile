<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;
use App\Models\PrivateMessage;
use App\Models\GroupMessage;

new class extends Component {
    
    #[Locked]
    public int $messageId = -1;

    #[Locked]
    public string $messageType = '';

    #[Locked]
    public bool $enabled = false;

    #[On('open-message-delete-popup')]
    public function open(int $messageId, string $messageType) {
        //We won't open the popup for a post id that cant exist
        if ($messageId < 0)
            return;

        $this->messageId = $messageId;
        $this->messageType = $messageType;
        $this->enabled = true;
    }

    #[On('close-message-delete-popup')]
    public function close(){
        $this->reset('messageId', 'messageType', 'enabled');
    }

    public function deletePost() {
        if ($this->enabled){
            // Charger le modèle correct en fonction de messageType
            $messageClass = $this->messageType === 'PrivateMessage' ? PrivateMessage::class : GroupMessage::class;

            $message = $messageClass::find($this->messageId);
            if ($message != null) {
                $message->delete();
                $this->dispatch('reset-message-views');
            }

            $this->close();
        }
    }


}; ?>

<div class="{{ $enabled ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
    <div class="sm:w-6/12 top-1/4 w-full p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button wire:click='close' title="Fermez le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="inline">
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">
                Êtes-vous sûr de vouloir supprimer ce message?
            </span>
            <span class="text-l flex flex-row pb-2 text-black dark:text-white">
                Celui-ci sera supprimé définitivement.
            </span>

            <div class="flex justify-end mt-4">
                <button type="button" wire:click='close'
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="button" wire:click='deletePost'
                    class="px-4 py-2 bg-gray-800 hover:bg-red-600 dark:hover:bg-red-500 dark:bg-gray-200 rounded text-white dark:text-black transition ease-in-out duration-150">
                    Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
    
    <!-- Script with the function to show the post delete popup -->
    <script>
        function showMessageDeletePopup(messageId = -1, messageType = '') {
            //Envoyer l'event pour activer le post editor{
            this.dispatchEvent(
                new CustomEvent('open-message-delete-popup', {
                    detail: {
                        messageId: messageId,
                        messageType : messageType,
                    }
                })
            );
        }
    </script>
</div>