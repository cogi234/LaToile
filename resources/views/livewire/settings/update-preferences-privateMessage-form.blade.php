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

<!-- Section message privé -->
<form wire:submit="updatePreferences" class="mt-6 space-y-6">
    <div>
        <span class="text-gray-900 dark:text-gray-100">Qui peut m'envoyer des messages privés?</span><br>
        <div class="flex flex-row mt-1">
            <input type="checkbox" wire:click='updatePreferences' wire:model='can_get_messages_from_anyone'
                id="can_get_messages_from_anyone" name="can_get_messages_from_anyone" class="size-4 mr-1" />
            <x-input-label for="can_get_messages_from_anyone"
                value="En cochant cette case, je peux recevoir des messages de personnes que je ne suis pas"
                class="inline" />
            <x-action-message class="me-3 ml-2" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
    </div>
</form>