<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Report;
use App\Models\Post;
use App\Models\Warning;
use App\Notifications\WarningUser;
use App\Models\PrivateMessage;
use App\Models\ReportMessage;

new class extends Component {

    public string $message = ''; // Message d'avertissement

    #[Locked]
    public int $userId = -1;

    #[Locked]
    public ?int $reportId = -1;

    #[Locked]
    public ?int $messageOrPostId = -1;

    #[Locked]
    public string $reportType = '';

    #[Locked]
    public bool $enabled = false;

    #[On('open-warning-modal')]
    public function open(int $userId, int $reportId, int $messageOrPostId, string $reportType)
    {
        // Charger le modèle correct en fonction de reportType
        $reportClass = $reportType === 'Report' ? Report::class : ReportMessage::class;

        $messageOrPost = $reportType === 'Report' ? Post::class : PrivateMessage::class;

        // Ne pas ouvrir le modal pour un utilisateur ou un message/post inexistant
        $user = User::find($userId);
        $messageOrPost::find($messageOrPostId);
        if ($user == null || $messageOrPost == null) return;

        $this->userId = $userId;
        $this->reportId = $reportId;
        $this->messageOrPostId = $messageOrPostId;
        $this->reportType = $reportType;
        $this->enabled = true;
        $this->resetValidation();
    }

    #[On('close-warning-modal')]
    public function close()
    {
        $this->reset('userId', 'reportId', 'messageOrPostId', 'reportType', 'enabled', 'message');
    }

    public function sendWarning()
    {
        if (!$this->enabled)
            return;

        $this->resetValidation();

        $textLength = strlen($this->message);

        // Charger le modèle correct en fonction de reportType
        $reportClass = $this->reportType === 'Report' ? Report::class : ReportMessage::class;

        // Validation de la raison et du datetime du rapport
        if ($this->message == null || $textLength < 5) {
            $this->addError('message', 'Votre message d\'avertissement doit contenir plus de 5 caractères.');
            return;
        }

        // Sauvegarder le warning dans la base de données
        Warning::create([
            'message' => strip_tags($this->message),
            'user_id' => $this->userId,
            'report_id' => $this->reportId,
            'report_type' => $this->reportType,
        ]);

        // Mettre à jour le rapport pour indiquer qu'il a été traité
        $report = $reportClass::find($this->reportId);
        if ($report) {
            $report->handled = 1;
            $report->save();
        }

        // Mettre à jour le post pour indiquer qu'il ne doit plus être visible
        if ($this->reportType == 'Report') {
            $post = Post::find($this->messageOrPostId);
            if ($post) {
                $post->hidden = 1;
                $post->save();
            }
        }

        // Envoyer une notificaiton à l'utilisateur
        User::find($this->userId)->notify(new WarningUser($this->message));

        $this->close();

        return redirect()->route('adminPage');
    }
};
?>

<div
    class="{{ $enabled ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
    <div
        class="sm:w-6/12 top-1/4 w-full p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="flex flex-row-reverse pb-2">
            <!-- Bouton de fermeture -->
            <button wire:click='close' title="Fermer le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form wire:submit.prevent='sendWarning'>
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Envoyer un avertissement</span>

            <!-- Raison du bannissement -->
            <textarea wire:model='message' placeholder="Raison de l'avertissement"
                class="p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-20 rounded"
                minlength="5" required></textarea>
            @error('message') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror

            <div class="flex justify-end mt-4">
                <button type="button" wire:click='close'
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-orange-600 dark:hover:bg-orange-400 dark:bg-gray-200 rounded text-white
                    dark:text-black transition ease-in-out duration-150">
                    Envoyer l'avertissement
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script pour ouvrir le formulaire de bannissement -->
<script>
    function showWarningModal(userId = -1, reportId = -1, messageOrPostId = -1, reportType = '') {
        this.dispatchEvent(
            new CustomEvent('open-warning-modal', {
                detail: {
                    userId: userId,
                    reportId : reportId,
                    messageOrPostId : messageOrPostId,
                    reportType : reportType,
                }
            })
        );
    }
</script>