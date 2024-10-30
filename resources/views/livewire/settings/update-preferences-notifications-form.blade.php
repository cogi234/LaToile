<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;

new class extends Component
{
    public bool $can_get_messages_from_anyone = true;
    public bool $can_get_messages_from_anyone2 = true;
    public bool $can_get_messages_from_anyone3 = true;
    public bool $can_get_messages_from_anyone4 = true;

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

<!-- Section notifications -->
<!-- Modifier wire:model, wire:click, id, label, etc. -->
<form wire:submit="updatePreferences" class="mt-6 space-y-6">
    <div>
        <span>Quel type de notification je souhaite recevoir?</span><br>
        <div class="flex flex-row">
            <input type="checkbox" wire:model='can_get_messages_from_anyone' id="can_get_messages_from_anyone"
                name="can_get_messages_from_anyone" class="size-4 mr-1" />
            <x-input-label for="can_get_messages_from_anyone" value="Notification de nouveau message" class="inline" />
            <x-action-message class="me-3 ml-2" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
        <div class="flex flex-row">
            <input type="checkbox" wire:model='can_get_messages_from_anyone2' id="can_get_messages_from_anyone2"
                name="can_get_messages_from_anyone" class="size-4 mr-1" />
            <x-input-label for="can_get_messages_from_anyone2" value="Notification de nouveau suivi" class="inline" />
            <x-action-message class="me-3 ml-2" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
        <div class="flex flex-row">
            <input type="checkbox" wire:model='can_get_messages_from_anyone3' id="can_get_messages_from_anyone3"
                name="can_get_messages_from_anyone" class="size-4 mr-1" />
            <x-input-label for="can_get_messages_from_anyone3" value="Notification de nouveau partage/commentaire"
                class="inline" />
            <x-action-message class="me-3 ml-2" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
        <div class="flex flex-row">
            <input type="checkbox" wire:model='can_get_messages_from_anyone4' id="can_get_messages_from_anyone4"
                name="can_get_messages_from_anyone" class="size-4 mr-1" />
            <x-input-label for="can_get_messages_from_anyone4" value="Notification de nouvelle mention j'aime"
                class="inline" />
            <x-action-message class="me-3 ml-2" on="preferences-updated">
                Sauvegardé.
            </x-action-message>
        </div>
    </div>
</form>