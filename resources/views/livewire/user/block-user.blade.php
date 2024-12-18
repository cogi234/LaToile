<?php 

namespace App\Http\Livewire\User;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public $userId;
    public $isBlocked = false;
    public $userName;

    public function mount($userId)
    {
        $this->userId = $userId;
        $user = User::find($this->userId);
        $this->userName = $user->name;
        $this->isBlocked = auth()->user()->blocked_users->contains($user);
    }

    public function toggleBlock()
    {
        $authUser = Auth::user();

        if ($authUser->blocked_users->contains($this->userId)) {
            $authUser->blocked_users()->detach($this->userId);
            $this->isBlocked = false;
            $this->dispatch('blocked-change');
        } else {
            $authUser->blocked_users()->attach($this->userId);
            $this->isBlocked = true;
            $this->dispatch('blocked-change');
        }
    }

}
?>

<div>
    <span class="flex px-4 py-2 hover:bg-gray-200 hover:dark:bg-gray-600 items-center text-gray-800 dark:text-gray-200">
        <button wire:click="toggleBlock" title="Bloquer {{$this->userName}} ?" class="flex flex-row btn {{ $isBlocked ? 'btn-danger' : 'btn-warning' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
            </svg>   
            {{ $isBlocked ? 'Débloquer ' . $this->userName : ' Bloquer ' . $this->userName }}
        </button>  
    </span>
</div>

