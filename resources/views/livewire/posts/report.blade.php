<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;
use App\Models\User;
use App\Models\Report;
use App\Notifications\ReportConfirmation;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    public string $reason = ''; // La raison du report

    #[Locked]
    public int $postId = -1;

    #[Locked]
    public bool $enabled = false;

    #[On('open-report-modal')]
    public function open(int $postId) {
        // Ne pas ouvrir le modal pour un post inexistant
        $post = Post::find($postId);
        if ($post == null) return;

        $this->postId = $postId;
        $this->enabled = true;
        $this->resetValidation();
    }

    #[On('close-report-modal')]
    public function close() {
        $this->reset('postId', 'enabled', 'reason');
    }

    public function submitReport() {
        if (!$this->enabled) return;

        $this->resetValidation();

        // Validation de la raison du rapport
        if ($this->reason == null) {
            $this->addError('reason', 'Aucune raison n\'a été sélectionnée.');
            return;
        }

        // Sauvegarder le rapport dans la base de données
        Report::create([
            'reason' => strip_tags($this->reason),
            'post_id' => $this->postId,
            'user_id' => Auth::id()
        ]);

        // Envoyer une notificaiton à l'utilisateur
        Auth::user()->notify(new ReportConfirmation(Post::find($this->postId), $this->reason));

        $this->close();
        $this->dispatch('reset-reports');
    }
};
?>

<div
    class="{{ $enabled ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
    <div
        class="sm:w-6/12 top-1/4 w-full p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="flex flex-row-reverse pb-2">
            <!-- Bouton pour fermer -->
            <button wire:click='close' title="Fermez le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <form wire:submit.prevent='submitReport'>
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Signaler le post</span>

            <!-- Sélecteur de raisons -->
            <select wire:model='reason'
                class="p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white rounded">
                <option title="Veuillez sélectionner une raison de signalement" value="">-- Sélectionnez une raison de signalement --</option>
                <option title="Exemples : discours haineux, incitations à la violence..." value="Haine">Haine ou incitation à la haine</option>
                <option title="Exemples : contenu pour adulte, suicide..." value="Contenu/Comportement inapproprié">Contenu/Comportement inapproprié</option>
                <option title="Exemples : attaques personnelles, intimidation..." value="Harcèlement">Harcèlement</option>
                <option title="Exemples : menaces, encouragement à la violence..." value="Menace ou incitation à la violence">Menace ou incitation à la violence</option>
                <option title="Exemples : faux profils, usurpation d'identité..." value="Usurpation d'identité">Usurpation d'identité</option>
                <option title="Exemples : escroqueries, fausses promesses, informations trompeuses..." value="Anarque, fraude ou fausses informations">Anarque, fraude ou fausses informations</option>
                <option title="Exemples : contenu répétitif, trop long inutilement..." value="Spam">Spam</option>
                <option title="Exemples : tags offensants ou inapproprié" value="Tag(s) inapproprié(s)">Tag(s) inapproprié(s)</option>            
            </select>

            @error('reason') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror

            <div class="flex justify-end mt-4">
                <button type="button" wire:click='close'
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 dark:hover:bg-white dark:bg-gray-200 rounded text-white
                    dark:text-black transition ease-in-out duration-150">
                    Signaler
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Script pour ouvrir le formulaire de signalement -->
<script>
    function showReportModal(postId = -1) {
            this.dispatchEvent(
                new CustomEvent('open-report-modal', {
                    detail: {
                        postId: postId
                    }
                })
            );
        }
</script>