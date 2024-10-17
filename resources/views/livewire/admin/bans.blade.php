<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Ban;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    public string $reason = '';     // La raison du rapport
    public string $banEndTime = ''; // La date de fin du bannissement

    #[Locked]
    public int $userId = -1;

    #[Locked]
    public ?int $reportId = null;

    #[Locked]
    public bool $enabled = false;

    #[On('open-banUser-modal')]
    public function open(int $userId, int $reportId)
    {
        // Ne pas ouvrir le modal pour un utilisateur inexistant
        $user = User::find($userId);
        if ($user == null) return;

        $this->userId = $userId;
        $this->reportId = $reportId;
        $this->enabled = true;
        $this->resetValidation();
    }

    #[On('close-banUser-modal')]
    public function close()
    {
        $this->reset('userId', 'reportId', 'enabled', 'banEndTime','reason');
    }

    public function banUser()
    {
        if (!$this->enabled) return;

        $this->resetValidation();

        // Vérifier si l'utilisateur est un administrateur
        $userToBan = User::find($this->userId);
        if ($userToBan && $userToBan->moderator) { // Remplacez 'role' et 'admin' par les valeurs appropriées
            $this->addError('user', 'Vous ne pouvez pas bannir un administrateur.');
            return;
        }

        $textLength = strlen($this->reason);

        // Validation de la raison et du datetime du rapport
        if ($this->reason == null || $textLength < 5) {
            $this->addError('reason', 'Votre raison doit contenir plus de 5 caractères.');
            return;
        }
        if ($this->banEndTime == null || $this->banEndTime <= now()) {
            $this->addError('banEndTime', 'Vous devez sélectionner une date qui est dans le futur.');
            return;
        }

        // Sauvegarder le ban dans la base de données
        Ban::create([
            'reason' => strip_tags($this->reason),
            'end_time' => $this->banEndTime,
            'user_id' => $this->userId,
            'report_id' => $this->reportId
        ]);

        // Mettre à jour le rapport pour indiquer qu'il a été traité
        DB::table('reports')
            ->where('id', $this->reportId)
            ->update(['handled' => 1]);

        // À FAIRE : CACHER LE POST DE L'UTILISATEUR BANNIS

        $this->close();

        return redirect()->route('adminPage');
    }
};
?>

<div
    class="{{ $enabled ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
    <div
        class="md:w-6/12 top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
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

            @error('reason') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror

            <!-- Sélection de la date de fin du bannissement -->
            <div class="mt-4">
                <label for="banEndTime" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
                    Date de fin du bannissement
                </label>
                <input wire:model="banEndTime" type="date"
                    class="p-2 rounded-md cursor-pointer dark:bg-gray-100/90 bg-gray-200 dark:bg-gray-300" required />
                @error('banEndTime') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror
                @error('user') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror
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
    function showBanUserModal(userId = -1, reportId = -1) {
            this.dispatchEvent(
                new CustomEvent('open-banUser-modal', {
                    detail: {
                        userId: userId,
                        reportId : reportId
                    }
                })
            );
        }
</script>