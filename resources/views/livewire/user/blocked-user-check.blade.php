<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Notifications\UserFollowed;
use App\Models\User;

new class extends Component {

    #[Locked]
    public $id;

    #[Locked]
    public $Currentid;

    #[Locked]
    public $isBlocked;

    public function mount(int $id)
    {
        $this->id = $id;
        $this->Currentid = Auth::user()->id;
        $this->isBlocked = $this->checkBlocked();
    }

    public function checkBlocked(): bool
    {
        // Check if the given user ID is in the list of blocked users
        return Auth::user()->blocked_users()->where('blocked', $this->id)->exists();
    }

    #[On('blocked-change')] 
    public function updateBlockedChange()
    {
        $this->isBlocked = $this->checkBlocked();
    }
};
?>

<div>
    @if($isBlocked)
        <span title="Cet utilisateur est bloqué" class="text-red-600 flex items-center">
            <!-- Blocked SVG Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>              
            Vous avez bloqué cet utilisateur
        </span>
    @endif
</div>
