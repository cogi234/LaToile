<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Draft;

new class extends Component {
    
    #[Locked]
    public int $draftId = -1;
    #[Locked]
    public bool $enabled = false;

    #[On('open-draft-delete-popup')]
    public function open(int $draftId) {
        //We won't open the popup for a draft id that cant exist
        if ($draftId < 0)
            return;

        $this->draftId = $draftId;
        $this->enabled = true;
    }

    #[On('close-draft-delete-popup')]
    public function close(){
        $this->reset('draftId', 'enabled');
    }

    public function deleteDraft() {
        if ($this->enabled){
            $draft = Draft::find($this->draftId);
            if ($draft != null) {
                $draft->delete();
                $this->dispatch('reset-draft-views');
            }

            $this->close();
        }
    }


}; ?>

<div class="{{ $enabled ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
    <div class="md:w-6/12 top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
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
                Êtes-vous sûr de vouloir supprimer ce brouillon?
            </span>
            <span class="text-l flex flex-row pb-2 text-black dark:text-white">
                Celui-ci sera supprimé définitivement.
            </span>

            <div class="flex justify-end mt-4">
                <button type="button" wire:click='close'
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="button" wire:click='deleteDraft'
                    class="px-4 py-2 bg-gray-800 hover:bg-red-600 dark:hover:bg-red-500 dark:bg-gray-200 rounded text-white dark:text-black transition ease-in-out duration-150">
                    Supprimer définitivement
                </button>
            </div>
        </div>
    </div>
    
    <!-- Script with the function to show the draft delete popup -->
    <script>
        function showDraftDeletePopup(draftId = -1) {
            //Envoyer l'event pour activer le draft delete popup{
            this.dispatchEvent(
                new CustomEvent('open-draft-delete-popup', {
                    detail: {
                        draftId: draftId
                    }
                })
            );
        }
    </script>
</div>