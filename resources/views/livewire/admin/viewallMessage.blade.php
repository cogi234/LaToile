<?php

use Livewire\Volt\Component;
use App\Models\ReportMessage;

new class extends Component {
    public $reportedMessages;

    public function mount()
    {
        // Correctly assign to the plural property
        $this->reportedMessages = ReportMessage::select('*')
            ->where('handled', 0) 
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();
    }
};

?>

<div>
    <div>
        <h5>Messages Reporter</h5>

        <div class="ml-4 text-gray-900 dark:text-gray-100 mb-4">

            <div class="flex sm:flex-row flex-col sm:space-y-0 space-y-8 items-center">
                @if($reportedMessages->isEmpty())
                    <p>No unhandled reported messages found.</p>
                @else
                    <ul>
                        @foreach($reportedMessages as $message)
                        <div class="border border-gray-300 rounded-lg p-4 mb-4 shadow h-32 flex items-center">
                            <li class="flex flex-wrap">
                                <span class="mr-4"><strong>Reason:</strong> {{ $message->reason }}</span>
                                <span class="mr-4"><strong>Message Type:</strong> {{ $message->message_type }}</span>
                                <span class="mr-4"><strong>Created At:</strong> {{ $message->created_at->format('Y-m-d H:i:s') }}</span>
                                <span><strong>Handled:</strong> {{ $message->handled ? 'Yes' : 'No' }}</span>
                            </li>
                        </div>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

    </div>
</div>
