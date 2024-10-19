<?php
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\PrivateMessage;
use App\Models\User;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $messageContent = "";
    public $privateMessages = [];
    public $selectedConversation = [];
    public $targetUser = null;
    public ?int $targetUserId = null;
    public ?int $currentUserId = null;
    public $uniqueSenderIds = [];
    public $editingMessageId = null;
    public $uniqueSenderIdsFromSenders = [];

    public function mount(?int $targetUserId, ?int $currentUserId)
    {
        $this->targetUserId = $targetUserId;
        $this->currentUserId = $currentUserId;
        
        if ($this->targetUserId !== null && $currentUserId !== null) {

            if(($targetUserId == $currentUserId)){
                $this->redirect('/messages/');
                return;
            }

            $this->targetUser = User::find($this->targetUserId);
            if (!$this->targetUser) {   
                session()->flash('error', 'Utilisateur non trouvé');
                return;
            }
        }
        
        $this->privateMessages = PrivateMessage::where(function($query) {
            $query->where('sender_id', Auth::id())
                ->orWhere('receiver_id', Auth::id());
        })->orderBy('created_at', 'desc')->get();

        $this->uniqueSenderIds = $this->privateMessages->pluck('receiver_id')
            ->unique()
            ->reject(function ($id) {
                return $id == Auth::id();
            })
            ->values()
            ->toArray();

        $this->uniqueSenderIdsFromSenders = $this->privateMessages->where('receiver_id', Auth::id())
            ->pluck('sender_id')
            ->unique()
            ->values()
            ->toArray();

        $this->uniqueSenderIds = array_unique(array_merge($this->uniqueSenderIds, $this->uniqueSenderIdsFromSenders));

        $this->selectedConversation = $this->getConversation($this->currentUserId, $this->targetUserId);
    }

    private function getConversation($senderId, $receiverId)
    {
        return PrivateMessage::where(function($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $senderId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function($query) use ($senderId, $receiverId) {
            $query->where('sender_id', $receiverId)
                ->where('receiver_id', $senderId);
        })->get();
    }


    #[Validate(['messageContent' => 'required|string|max:255'])]
    public function send()
    {
        if (trim($this->messageContent) === '') {
            $this->redirect('/messages/' . $this->currentUserId . '-' . $this->targetUserId);
        }

        $this->validate();

        PrivateMessage::create([
            'message' => $this->messageContent,
            'read' => false,
            'sender_id' => $this->currentUserId,
            'receiver_id' => $this->targetUserId,
            'created_at' => now(),
        ]);

        $this->messageContent = '';

        $this->redirect('/messages/' . $this->currentUserId . '-' . $this->targetUserId);
    }

    public function startEditing($messageId)
    {
        $this->editingMessageId = $messageId;
        $message = PrivateMessage::find($messageId);
        $this->messageContent = $message->message;
    }

    public function cancelEditing()
    {
        $this->editingMessageId = null;
        $this->messageContent = '';
    }
    
    public function saveEdit($messageId, $newContent)
    {
        if (trim($newContent) === '') {
            return;
        }

        $message = PrivateMessage::find($messageId);
        if ($message && $message->sender_id == $this->currentUserId) {
            $message->update([
                'message' => $newContent,
            ]);
        }
    }

    public function deleteMessage($messageId)
    {
        PrivateMessage::find($messageId)->delete();
        $this->redirect('/messages/' . $this->currentUserId . '-' . $this->targetUserId);
        session()->flash('success', 'Message supprimé avec succès');
    }

    public function getSenders()
    {
        $senders = Cache::remember('senders_' . Auth::id(), 60, function() {
            return User::whereIn('id', $this->uniqueSenderIds)->get();
        });

        return $senders;
    }

    public function render(): mixed
    {
        $senders = $this->getSenders();

        $privateMessages = PrivateMessage::where('sender_id', Auth::id())
        ->orWhere('receiver_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->paginate(10);

        return view('livewire.messages.messageBoard', [
            'senders' => $senders,
            'privateMessages' => $privateMessages,
        ]);
    }
};?>

<x-app-layout>
    <div class="grid grid-cols-2 h-full bg-white dark:bg-gray-800">
        <div id="conversations" class="border-r-2 h-full overflow-y-auto">
            <div class="flex flex-row justify-between items-center p-4 bg-gray-100 dark:bg-gray-700">
                <div class="text-xl font-semibold dark:text-white">Messages</div>
                <div id="option" class="text-gray-500 dark:text-gray-300" title="Créer un groupe de discussion">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
            @if($privateMessages->isEmpty() && $targetUserId == null)
                <div class="p-4">
                    <p class="text-gray-500 dark:text-gray-300">
                        Bienvenu à votre messagerie. Écrivez entre vous et les autres sur LaToile
                    </p>
                </div>
            @else
                <div x-data="{ query: '' }">
                    <!-- Search Bar -->
                    <div class="p-4" >
                        <div class="relative">
                            <input type="text" name="query" id="searchBar" x-model="query"
                                class="block w-full pl-10 pr-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-900 dark:text-white rounded-full focus:outline-none focus:bg-white focus:text-gray-900 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                                placeholder="Rechercher des Messages Directs"/>
                    
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-4.35-4.35m2.1-6.95a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                
                    <!-- List of Private Messages -->
                    @if($privateMessages->isEmpty() && $targetUserId !== null)
                        <div>
                            <div class="p-4 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                <div class="flex items-start">
                                    <div id="avatar">
                                        <img class="w-12 h-12 rounded-full mr-4 shadow-lg" alt="Profile Image"
                                            src="{{ $targetUser->getAvatar() }}"/>
                                    </div>
                                    <div id="Name">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $targetUser->name }}
                                        </p>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                    @else 
                        <div>
                            @if (count($uniqueSenderIds) > 0)
                                @foreach ($uniqueSenderIds as $uniqueSenderId)
                                    @php
                                        $sender = User::find($uniqueSenderId);
                                        $unreadMessages = \App\Models\PrivateMessage::where('sender_id', $uniqueSenderId)
                                            ->where('receiver_id', Auth::id())
                                            ->where('read', 0)
                                            ->count();
                                    @endphp
                                    <div x-show="query === '' || '{{ strtolower($sender->name) }}'.includes(query.toLowerCase())"
                                        class="p-4 max-h-[calc(100vh-200px)] hover:bg-gray-200 dark:hover:bg-gray-900 cursor-pointer overflow-y-auto 
                                        @if($uniqueSenderId == $targetUserId) bg-gray-100 dark:bg-gray-700 @endif">
                                        <a href="{{ url('messages/' . Auth::id() . '-' . $uniqueSenderId) }}">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <img class="h-10 w-10 rounded-full"
                                                        src="{{ $sender->getAvatar() }}" alt="Avatar de {{ $sender->name }}" />
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $sender->name }}
                                                    </p>
                                                    @if($unreadMessages > 0)
                                                        <span class="text-xs text-red-500">Nouveau message</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Private Message Area -->
        @if($targetUser !== null)
            <div id="privateMessage" class="h-full flex flex-col">
                <div id="infoDiscussion" class="flex items-center pl-4 pt-2">
                    <div id="avatar">
                        <img class="w-12 h-12 rounded-full mr-4 shadow-lg" alt="Profile Image"
                            src="{{ $targetUser->getAvatar() }}"/>
                    </div>
                    <div id="Name">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $targetUser->name }}
                        </p>
                    </div>
                </div>
                <!-- Zone de discussion -->
                <div id="discussion" class="flex-1 p-4 max-h-[calc(100vh-150px)] overflow-y-auto">
                    @if ($selectedConversation)
                        @foreach ($selectedConversation as $message)
                            @php
                                $isCurrentUserMessage = $message->sender_id == $currentUserId;
                                $currentTimeZone = 'America/Toronto';
                                $timeFormat = 'Y-m-d H:i';
                            @endphp
  
                            <div wire:key='{{ $message->id }}' class="p-2 flex {{ $isCurrentUserMessage ? 'justify-end' : 'justify-start' }}">
                                <!-- Message Container -->
                                @if($isCurrentUserMessage)
                                    <div 
                                        title="{{ $message->updated_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                                        id="message_{{ $message->id }}" 
                                        class="max-w-xs w-auto p-3 rounded-lg bg-blue-500 text-white" 
                                        x-data="{ editing: false, messageContent: `{{ $message->message }}` }"
                                        @click="editing = true"
                                        @keydown.escape.window="editing = false"
                                        @click.outside="editing = false">

                                        <p x-show="!editing">{{ $message->message }}</p>
                                        
                                        <!-- Edit Box -->
                                        <template x-if="editing">
                                            <div class="mt-2">
                                                <input type="text" 
                                                    x-model="messageContent" 
                                                    class="w-full border rounded p-2 text-gray-900"
                                                    @keydown.enter="editing = false; $wire.saveEdit({{ $message->id }}, messageContent)">
                                                <div class="flex justify-end space-x-2 mt-2">
                                                    <!-- Save (Edit) Button -->
                                                    <button type="button"
                                                    @click="editing = false; $wire.saveEdit({{ $message->id }}, messageContent)"
                                                    class="px-3 py-1 bg-green-500 text-white rounded flex items-center">
                                                        <i class="fas fa-save mr-2"></i> Enregistrer
                                                    </button>

                                                    <!-- Delete Button -->
                                                    <button type="button"
                                                    @click="if (confirm('Êtes-vous sûr de vouloir supprimer ce message?')) { $wire.deleteMessage({{ $message->id }}) }"
                                                    class="px-3 py-1 bg-red-500 text-white rounded flex items-center">
                                                        <i class="fas fa-trash mr-2"></i> Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                @else
                                    <div title="{{ $message->updated_at->setTimezone($currentTimeZone)->format($timeFormat) }}" class="max-w-xs w-auto p-3 rounded-lg bg-gray-300 text-gray-900">
                                        <p>{{ $message->message }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div class="flex items-center justify-center h-full">
                            <p class="text-gray-500 dark:text-gray-300">Sélectionnez une conversation pour commencer</p>
                        </div>
                    @endif
                </div>
                
                <!-- Barre de message -->
                <div id="messageBar" class="p-4 bg-gray-100 dark:bg-gray-700 border-t dark:border-gray-600">
                    <form wire:submit.prevent="send" class="flex items-center">
                        <!-- Champ de texte pour écrire le message -->
                        <input type="text" wire:model="messageContent" placeholder="Écrire un message..."
                            class="flex-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
                        
                        <!-- Bouton d'envoi avec une icône d'avion en papier -->
                        <button type="submit" aria-label="Envoyer un message" class="ml-2 p-2 bg-indigo-500 text-white rounded-full hover:bg-indigo-600 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v6l16-8-16-8v6l10 2-10 2z" />
                            </svg>
                        </button>
                    </form>
                </div>                
            </div>
        @else
        <div class="p-4">
            <p class="text-gray-500 dark:text-gray-300">
                Sélectionner un usager pour voir vos conversations
            </p>
        </div>
        @endif
    </div>
    <style>
        main{
            height: 100vh;
        }
    
        #discussion::-webkit-scrollbar {
            width: 0;
            height: 0;
        }
    
        #discussion {
            scrollbar-width: none;
        }
    
        #discussion {
            -ms-overflow-style: none;
            overflow-y: scroll;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</x-app-layout>

