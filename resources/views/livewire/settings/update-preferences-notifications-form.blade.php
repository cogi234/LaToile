<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;

new class extends Component
{
    // Notifications options
    public bool $can_get_notification_from_message = true;
    public bool $can_get_notification_from_follow = true;
    public bool $can_get_notification_from_share = true;
    public bool $can_get_notification_from_like = true;
    public bool $can_get_notification_from_group_invitation = true;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->can_get_notification_from_message = Auth::user()->can_get_notification_from_message;
        $this->can_get_notification_from_follow = Auth::user()->can_get_notification_from_follow;
        $this->can_get_notification_from_share = Auth::user()->can_get_notification_from_share;
        $this->can_get_notification_from_like = Auth::user()->can_get_notification_from_like;
        $this->can_get_notification_from_group_invitation = Auth::user()->can_get_notification_from_group_invitation;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updatePreferences(): void
    {
        $user = Auth::user();

        $user->can_get_notification_from_message = $this->can_get_notification_from_message;
        $user->can_get_notification_from_follow = $this->can_get_notification_from_follow;
        $user->can_get_notification_from_share = $this->can_get_notification_from_share;
        $user->can_get_notification_from_like = $this->can_get_notification_from_like;
        $user->can_get_notification_from_group_invitation = $this->can_get_notification_from_group_invitation;

        $user->save();

        $this->dispatch('preferences-updated', name: $user->name);
    }
}; ?>

<!-- Section notifications -->
<form wire:submit="updatePreferences" class="mt-6 space-y-6">
    <div>
        <span class="text-gray-900 dark:text-gray-100">Quel type de notification souhaitez-vous recevoir?</span><br>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Si vous décochez toutes les cases, seules les notifications essentielles seront envoyées.
        </p>
        <div class="flex flex-row sm:mb-2 sm:mt-1 pt-2 mb-5">
            <input type="checkbox" wire:click='updatePreferences' wire:model='can_get_notification_from_message' id="can_get_notification_from_message"
                name="can_get_notification_from_message" class="size-4 mr-1" />
            <x-input-label for="can_get_notification_from_message" value="Recevoir les notifications de nouveau message." class="inline" />
        </div>
        <div class="flex flex-row mt-1 sm:mb-2 mb-5">
            <input type="checkbox" wire:click='updatePreferences' wire:model='can_get_notification_from_group_invitation' id="can_get_notification_from_group_invitation"
                name="can_get_notification_from_group_invitation" class="size-4 mr-1" />
            <x-input-label for="can_get_notification_from_group_invitation" value="Recevoir les notifications d'invitation à un groupe." class="inline" />
        </div>
        <div class="flex flex-row mt-1 sm:mb-2 mb-5">
            <input type="checkbox" wire:click='updatePreferences' wire:model='can_get_notification_from_follow' id="can_get_notification_from_follow"
                name="can_get_notification_from_follow" class="size-4 mr-1" />
            <x-input-label for="can_get_notification_from_follow" value="Recevoir les notifications de nouveau suivi." class="inline" />
        </div>
        <div class="flex flex-row mt-1 sm:mb-2 mb-5">
            <input type="checkbox" wire:click='updatePreferences' wire:model='can_get_notification_from_share' id="can_get_notification_from_share"
                name="can_get_notification_from_share" class="size-4 mr-1" />
            <x-input-label for="can_get_notification_from_share" value="Recevoir les notifications de nouveau partage ou commentaire."
                class="inline" />
        </div>
        <div class="flex flex-row mt-1 sm:mb-0 mb-2">
            <input type="checkbox" wire:click='updatePreferences' wire:model='can_get_notification_from_like' id="can_get_notification_from_like"
                name="can_get_notification_from_like" class="size-4 mr-1" />
            <x-input-label for="can_get_notification_from_like" value="Recevoir les notifications de nouvelle mention j'aime."
                class="inline" />
        </div>
        <x-action-message class="mt-2" on="preferences-updated">
            Préférences de notifications sauvegardé.
        </x-action-message>
    </div>
</form>