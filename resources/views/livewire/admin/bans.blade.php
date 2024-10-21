<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Ban;
use App\Models\Report;
use App\Models\Post;

new class extends Component {

    public string $reason = '';     // La raison du rapport
    public ?string $banEndTime = null; // La date de fin du bannissement
    public bool $permanent = false;

    #[Locked]
    public int $userId = -1;

    #[Locked]
    public ?int $reportId = -1;

    #[Locked]
    public ?int $postId = -1;

    #[Locked]
    public bool $enabled = false;

    #[On('open-banUser-modal')]
    public function open(int $userId, int $reportId, int $postId)
    {
        // Ne pas ouvrir le modal pour un utilisateur ou un post inexistant
        $user = User::find($userId);
        $post = Post::find($postId);
        if ($user == null || $post == null) return;

        $this->userId = $userId;
        $this->reportId = $reportId;
        $this->postId = $postId;
        $this->enabled = true;
        $this->resetValidation();
    }

    #[On('close-banUser-modal')]
    public function close()
    {
        $this->reset('userId', 'reportId', 'enabled', 'banEndTime', 'permanent','reason');
    }

    public function banUser()
    {
        if (!$this->enabled)
            return;

        if (!$this->banEndTime)
            $this->banEndTime = null;

        $this->resetValidation();

        // Vérifier si l'utilisateur est un administrateur
        $userToBan = User::find($this->userId);
        if ($userToBan && $userToBan->moderator) {
            $this->addError('user', 'Vous ne pouvez pas bannir un administrateur.');
            return;
        }

        $textLength = strlen($this->reason);

        // Validation de la raison et du datetime du rapport
        if ($this->reason == null || $textLength < 5) {
            $this->addError('reason', 'Votre raison doit contenir plus de 5 caractères.');
            return;
        }
        if (!$this->permanent && $this->banEndTime == null || !$this->permanent && $this->banEndTime <= now()) {
            $this->addError('banEndTime', 'Vous devez sélectionner une date qui est dans le futur.');
            return;
        }

        // Sauvegarder le ban dans la base de données
        Ban::create([
            'reason' => strip_tags($this->reason),
            'end_time' => $this->banEndTime,
            'user_id' => $this->userId,
            'report_id' => $this->reportId,
        ]);

        // Mettre à jour le rapport pour indiquer qu'il a été traité
        $report = Report::find($this->reportId);
        if ($report) {
            $report->handled = 1;
            $report->save();
        }

        // Mettre à jour le post pour qu'il ne doit plus être visible
        $post = Post::find($this->postId);
        if ($post) {
            $post->hidden = 1;
            $post->save();
        }

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

        <form wire:submit.prevent='banUser'>
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Bannir l'utilisateur</span>

            <!-- Raison du bannissement -->
            <textarea wire:model='reason' placeholder="Raison du bannissement"
                class="p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-20 rounded"
                minlength="5" required></textarea>
            @error('reason') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror

            <!-- Bannissement permanent -->
            <div class="mt-4">
                <label for="isPermanent" class="inline-flex items-center">
                    <input wire:model="permanent" type="checkbox" id="isPermanent" class="form-checkbox" onclick="toggleBanEndTime()">
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-200">
                        Bannissement permanent
                    </span>
                </label>
            </div>

            <!-- Sélection de la date de fin du bannissement -->
            <div class="mt-4" id="banEndTimeField">
                <label for="banEndTime" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Date de fin du bannissement
                </label>
                <input wire:model="banEndTime" type="date" id="banEndTime"
                    class="p-2 rounded-md cursor-pointer dark:bg-gray-100/90 bg-gray-200 dark:bg-gray-300" required />
                @error('banEndTime') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror
            </div>

            <div class="flex justify-end mt-4">
                <button type="button" wire:click='close'
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-red-500 dark:hover:bg-red-400 dark:bg-gray-200 rounded text-white
                    dark:text-black transition ease-in-out duration-150">
                    Bannir l'utilisateur
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script pour ouvrir le formulaire de bannissement -->
<script>
    function showBanUserModal(userId = -1, reportId = -1, postId = -1) {
        this.dispatchEvent(
            new CustomEvent('open-banUser-modal', {
                detail: {
                    userId: userId,
                    reportId : reportId,
                    postId : postId
                }
            })
        );
    }
    function toggleBanEndTime() {
        var isPermanent = document.getElementById('isPermanent').checked;
        var banEndTimeField = document.getElementById('banEndTimeField');
        var banEndTimeInput = document.getElementById('banEndTime');

        if (isPermanent) {
            banEndTimeField.style.display = 'none'; // Ne plus affiché le calendrier
            banEndTimeInput.required = false;       // N'est plus requis dans le form
            banEndTimeInput.value = '';             // Réinitialiser la valeur si le champ est masqué
        } else {
            banEndTimeField.style.display = 'block';
            banEndTimeInput.required = true;
        }
    }
</script>