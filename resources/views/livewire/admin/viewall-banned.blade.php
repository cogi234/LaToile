<?php

use Livewire\Volt\Component;
use App\Models\Ban; 
use Livewire\Attributes\On;

new class extends Component {
    public $bans;

    public function mount()
    {
        // Retrieve all bans from the database
        $this->bans = Ban::all();
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
                    <th>ID</th>
                    <th>Reason</th>
                    <th>End Time</th>
                    <th>User ID</th>
                    <th>Report ID</th>
                    <th>Report Type</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bans as $ban)
                    <tr>
                        <td>{{ $ban->id }}</td>
                        <td>{{ $ban->reason }}</td>
                        <td>{{ $ban->end_time ? \Carbon\Carbon::parse($ban->end_time)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <td>{{ $ban->user_id }}</td>
                        <td>{{ $ban->report_id ?? 'N/A' }}</td>
                        <td>{{ $ban->report_type }}</td>
                        <td>{{ $ban->created_at ? \Carbon\Carbon::parse($ban->created_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <td>{{ $ban->updated_at ? \Carbon\Carbon::parse($ban->updated_at)->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        <!-- débannir l'utilisateur -->
                        <td><livewire:admin.unban :userId="$ban->user_id"/></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
