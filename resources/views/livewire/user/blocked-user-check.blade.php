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
        <span class="text-red-600 flex items-center">
            <!-- Blocked SVG Icon -->
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 mr-2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
            </svg>
            Utilisateur Bloquer
        </span>
    @endif
</div>
