<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;

new class extends Component
{
    public bool $can_get_messages_from_anyone = true;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->can_get_messages_from_anyone = Auth::user()->can_get_messages_from_anyone;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updatePreferences(): void
    {
        $user = Auth::user();

        $user->can_get_messages_from_anyone = $this->can_get_messages_from_anyone;

        $user->save();

        $this->dispatch('preferences-updated', name: $user->name);
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Préférences
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Mettez à jour vos préférences pour l'utilisation du site.
        </p>
    </header>

    <form wire:submit="updatePreferences" class="mt-6 space-y-6">
        <div>
            <input type="checkbox" wire:model='can_get_messages_from_anyone' name="can_get_messages_from_anyone"
                class="rounded-xl size-6"/> 
            <x-input-label for="can_get_messages_from_anyone" value="Peut recevoir des messages de personnes que je ne suis pas" class="inline"/>
        </div>
        
        <div class="flex items-center gap-4">
            <x-primary-button>Sauvegarder</x-primary-button>

            <x-action-message class="me-3" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
    </form>
</section>
