<?php

use Livewire\Volt\Component;
use App\Models\ReportMessage;

new class extends Component {
    public $reportedMessages;

    public function mount()
    {
        // Correctly assign to the plural property
        $this->reportedMessages = ReportMessage::select('*')
            ->where('handled', 1) 
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();
    }
};

?>


<div>
    <h1>Reported Messages</h1>

    @if($reportedMessages->isEmpty())
        <p>No unhandled reported messages found.</p>
    @else
        <ul>
            @foreach($reportedMessages as $message)
                <li>
                    <strong>Reason:</strong> {{ $message->reason }}<br>
                    <strong>Message Type:</strong> {{ $message->message_type }}<br>
                    <strong>Created At:</strong> {{ $message->created_at->format('Y-m-d H:i:s') }}<br>
                    <strong>Handled:</strong> {{ $message->handled ? 'Yes' : 'No' }}<br>
                </li>
            @endforeach
        </ul>
    @endif
</div>
