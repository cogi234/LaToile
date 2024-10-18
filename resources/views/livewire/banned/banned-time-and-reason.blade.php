<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\User;
use App\Models\Ban;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    public string $reason;   // La raison du rapport
    public ?string $endTime; // La date de fin du bannissement

    public function mount()
    {
        // Vérifie si l'utilisateur est banni
        $ban = Ban::where('user_id', Auth::id())
            ->first();

        if ($ban) {
            $this->reason = $ban->reason;
            $this->endTime = $ban->end_time;
        } else {
            return redirect()->route('dashboard');
        }
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect()->route('dashboard');
    }
};
?>
@php
    date_default_timezone_set("America/Toronto");
@endphp
<div>
    <p class="text-sm text-gray-700 mb-6 dark:text-white">
        <strong>Raison :</strong> {{ $reason ? $reason : 'Raison inconnu' }}
    </p>
    <p class="text-sm text-gray-700 mb-6 dark:text-white">
        <strong>Temps de bannissement :</strong> {{ $endTime ? strftime('%d %B %Y', strtotime($endTime)) : 'Temps indéterminé' }}
    </p>

    <a wire:click="logout" href="{{ route('dashboard') }}" class="inline-block px-5 py-2 bg-gray-500 dark:bg-gray-200 text-white dark:text-gray-800 font-semibold rounded-lg shadow-md hover:bg-gray-600 dark:hover:bg-white transition duration-300">
        Se déconnecter
    </a>
</div>