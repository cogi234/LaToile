<x-app-layout>
    @foreach ($conversations as $conversation)
    <div class="conversation">
        <!-- Check if this conversation involves the target user and highlight it -->
        @if($conversation->receiver_id == $targetUserId || $conversation->sender_id == $targetUserId)
            <div class="conversation selected">
                <!-- Display the conversation between current and target users -->
                @foreach ($selectedConversation as $message)
                    <p><strong>{{ $message->sender->name }}:</strong> {{ $message->content }}</p>
                @endforeach
            </div>
        @else
            <div class="conversation">
                <!-- Display a summary of other conversations -->
                <p>Conversation with {{ $conversation->sender_id == $currentId ? $conversation->receiver->name : $conversation->sender->name }}</p>
            </div>
        @endif
    </div>
@endforeach
</x-app-layout>
