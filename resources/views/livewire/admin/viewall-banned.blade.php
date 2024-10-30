<?php

use Livewire\Volt\Component;
use App\Models\Ban; 
use App\Models\User;
use Livewire\Attributes\On;

new class extends Component {
    public $bans;
    public $posts;
    

    public function mount()
{
    $this->bans = Ban::with('user')->get();
}


};

?>

<div>
    <h2>Ban List</h2>

    @if($bans->isEmpty())
        <p>No bans found.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Raison</th>
                    <th>Date de fin</th>
                    <th>Nom User</th>
                    <th>Date de création</th>
                    <th>Date de modification</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bans as $ban)
                    <tr style="border: 1px solid #ccc; padding: 10px;">
                        <td class="px-10 text-wrap" style="max-width: 200px; word-break: break-word;">
                            {{ $ban->reason }}
                        </td>
                        <td class="px-10">{{ $ban->end_time ? \Carbon\Carbon::parse($ban->end_time)->format('Y-m-d H:i:s') : 'PERMANENT' }}</td>
                        <td class="px-10">{{ $ban->user ? $ban->user->name : 'Unknown User' }}</td>
                        <td class="px-10">{{ $ban->created_at ? \Carbon\Carbon::parse($ban->created_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <td class="px-10">{{ $ban->updated_at ? \Carbon\Carbon::parse($ban->updated_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <!-- débannir l'utilisateur -->
                        <td><livewire:admin.unban :userId="$ban->user_id"/></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

