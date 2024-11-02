<?php
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\PrivateMessage;
use App\Models\User;
use Astrotomic\Twemoji\Twemoji;

new class extends Component {

    public string $messageContent = "";
    public $privateMessages = [];
    public array $uniqueSenderIds = [];
    public $selectedConversation = [];
    public ?User $targetUser = null;
    public ?int $targetUserId = null;
    public ?int $editingMessageId = null;
    public string $editMessageContent = "";
    public $uniqueSenderIdsFromSenders = [];
    public $searchQuery = '';
    public $searchResults = [];

    public function mount(?int $targetUserId)
    {
        $this->updateConversations();

        if ($targetUserId !== null) {
            $this->setSelectedUser($targetUserId);

            $this->targetUser = User::find($this->targetUserId);
            if (!$this->targetUser) {   
                session()->flash('error', 'Utilisateur non trouvé');
                return;
            }

            PrivateMessage::where('sender_id', $this->targetUserId)
                ->where('receiver_id', Auth::id())
                ->where('read', 0) // Only update unread messages
                ->update(['read' => 1]);
        }
        
        $this->privateMessages = PrivateMessage::where(function($query) {
            $query->where('sender_id', Auth::id())
                ->orWhere('receiver_id', Auth::id());
        })->orderBy('created_at', 'desc')->get();
    }

    public function setSelectedUser(int $targetUserId) {
        if ($targetUserId == Auth::id()) {
            $this->targetUserId = null;
            $this->targetUser = null;
            return;
        }

        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            $this->targetUserId = null;
            $this->targetUser = null;
            return;
        }

        $this->targetUserId = $targetUserId;
        $this->targetUser = $targetUser;
        $this->updateSelectedConversation();
        $this->dispatch('updateSelectedConversation');
    }

    public function updateConversations() {
        $this->privateMessages = PrivateMessage::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->get();

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

        $this->updateSelectedConversation();
    }
    
    public function updateSelectedConversation() {
        $this->selectedConversation = PrivateMessage::where(function($query) {
            $query->where('sender_id', Auth::id())
                ->where('receiver_id', $this->targetUserId);
        })->orWhere(function($query) {
            $query->where('sender_id', $this->targetUserId)
                ->where('receiver_id', Auth::id());
        })->get();

    }

    public function send()
    {
        if (trim($this->messageContent) === '') {
            $this->addError('messageLength', 'Message vide.');
            $this->messageContent = '';
            return;
        }
        
        if (strlen(trim($this->messageContent)) > 2000) {
            $this->addError('messageLength', 'Message trop long (2000 caractères maximum).');
            $this->messageContent = '';
            return;
        }

        PrivateMessage::create([
            'message' => trim($this->messageContent),
            'sender_id' => Auth::id(),
            'receiver_id' => $this->targetUserId,
        ]);

        $this->messageContent = '';

        $this->updateSelectedConversation();
    }


    public function startEditing($messageId)
    {
        $this->editingMessageId = $messageId;
        $message = PrivateMessage::find($messageId);
        $this->editMessageContent = $message->message;
    }

    public function stopEditing()
    {
        $this->editingMessageId = null;
        $this->editMessageContent = '';
    }
    
    public function saveEdit()
    { 
        if ($this->editMessageContent == null || strlen(trim($this->editMessageContent)) <= 0 || strlen(trim($this->editMessageContent)) > 2000) {
            $this->addError('editMessageLength', 'Votre message est trop court ou trop long. (1-2000)');
            return;
        }

        $message = PrivateMessage::find($this->editingMessageId);
        if ($message && $message->sender_id == Auth::id()) {
            $message->message = trim($this->editMessageContent);
            $message->save();
        }
        $this->stopEditing();
        
        $this->updateSelectedConversation();
    }

    public function deleteMessage()
    {
        $message = PrivateMessage::find($this->editingMessageId);
        if ($message && $message->sender_id == Auth::id()) {
            $message->delete();
        }
        
        $this->updateSelectedConversation();
    }

    public function updatedSearchQuery()
    {
        $this->searchResults = User::where('name', 'like', $this->searchQuery . '%')->get();
    }

    public function getSenders()
    {
        $senders = Cache::remember('senders_' . Auth::id(), 60, function() {
            return User::whereIn('id', $this->uniqueSenderIds)->get();
        });

        return $senders;
    }
}
?>

<div wire:click='stopEditing' class="grid grid-cols-2 h-full bg-white dark:bg-gray-800">
    <div class="border-r-2 h-full overflow-y-auto">

        <!-- Board pour changer avec messages de groupe + création -->
        <livewire:messages.messageBoard :isGroup='false' wire:key='messageBoardComponent' />

        @if($privateMessages->isEmpty() && $targetUserId == null)
        <div class="p-4">
            <p class="text-gray-500 dark:text-gray-300">
                Bienvenue à votre messagerie. Écrivez entre vous et les autres sur La Toile
            </p>
        </div>
        @else
        <div>
            <div class="pt-4 pr-4 pb-2 pl-4 bg-gray-100 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Conversations</h3>
            </div>
            <!-- Search Bar -->
            <div class="p-4" >
                <div class="relative">
                    <input type="text" name="query" id="searchBar"
                        class="block w-full pl-10 pr-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-900 dark:text-white rounded-full focus:outline-none focus:bg-white focus:text-gray-900 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                        placeholder="Rechercher des Messages Directs"/>
            
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m2.1-6.95a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- List of Conversations -->
            <div>
                @if($targetUserId !== null && !in_array($targetUserId, $uniqueSenderIds))
                <div class="p-4 bg-gray-100 dark:bg-gray-700 cursor-pointer">
                    <div class="flex items-start">
                        <a class="flex flex-row" href="{{ url('user/' . $targetUser->id) }}">
                            <a class="flex flex-row" href="{{ url('user/' . $targetUser->id) }}">
                                <img class="w-12 h-12 rounded-full shadow-lg hover:outline hover:outline-2 hover:outline-black/10" alt="Avatar de {{ $targetUser->name }}" src="{{ $targetUser->getAvatar() }}"/>
                                <div class="flex self-center ml-3 text-sm font-medium text-gray-900 hover:underline hover:dark:underline dark:text-white transition ease-in-out duration-150">
                                    {{ $targetUser->name }}
                                </div>
                            </a>
                        </a>
                    </div>
                </div>
                @endif

                @foreach ($uniqueSenderIds as $uniqueSenderId)
                    @php
                        $sender = \App\Models\User::find($uniqueSenderId);
                    @endphp
                    <div wire:click='setSelectedUser({{ $uniqueSenderId }})' wire:key='conversation_{{ $uniqueSenderId }}'
                        class="p-4 max-h-[calc(100vh-200px)] hover:bg-gray-200 dark:hover:bg-gray-900 cursor-pointer overflow-y-auto 
                        @if ($uniqueSenderId == $targetUserId) bg-gray-100 dark:bg-gray-700 @endif">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full" src="{{ $sender->getAvatar() }}" alt="Avatar de {{ $sender->name }}"/>
                            <div class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $sender->name }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <!-- Private Message Area -->
    @if ($targetUser !== null)

    <div id="message_area" class="h-full flex flex-col overflow-y-scroll">
        <!-- Infos de la discussion -->
        <div class="flex items-center pl-4 pt-2">
            <a class="flex flex-row" href="{{ url('user/' . $targetUser->id) }}">
                <img class="w-12 h-12 rounded-full shadow-lg hover:outline hover:outline-2 hover:outline-black/10" alt="Avatar de {{ $targetUser->name }}" src="{{ $targetUser->getAvatar() }}"/>
                <div class="flex self-center ml-3 text-sm font-medium text-gray-900 hover:underline hover:dark:underline dark:text-white transition ease-in-out duration-150">
                    {{ $targetUser->name }}
                </div>
            </a>
        </div>
        <!-- Zone de discussion -->
        <div id="discussion" class="flex-1 p-4">
            @if ($selectedConversation)
                @foreach ($selectedConversation as $message)
                    @php
                        $isCurrentUserMessage = $message->sender_id == Auth::id();
                        $currentTimeZone = 'America/Toronto';
                        $timeFormat = 'Y-m-d H:i';
                        $messageText = $message->message;
                        $textWithURLS = preg_replace(
                            '/(https?:\/\/[^\s]+)/',
                            '<a href="$1" target="_blank" rel="noopener noreferrer" class="hover:underline">$1</a>',
                            $messageText
                        );
                        $messageText = Twemoji::text($textWithURLS)->svg()->toHTML();
                    @endphp

                    <div wire:key='message_{{ $message->id }}' class="p-2 flex {{ $isCurrentUserMessage ? 'justify-end' : 'justify-start' }}">
                        <!-- Message Container -->
                        @if($isCurrentUserMessage)
                        <div title="{{ $message->updated_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                            id="message_{{ $message->id }}" 
                            class="max-w-[60%] w-auto p-3 rounded-lg bg-blue-500 text-white cursor-pointer"
                            wire:click.stop='startEditing({{ $message->id }})'>

                            @if ($message->id == $editingMessageId)
                            <!-- Edit Box -->
                            <div>
                                <div class="mt-2">
                                    <textarea maxlength="2000" minlength="1"
                                        wire:model="editMessageContent" 
                                        wire:keydown.enter="saveEdit" 
                                        wire:keydown.escape="stopEditing"
                                        class="p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white h-10 min-h-10 rounded">
                                    </textarea>
                                    @error('editMessageLength') <div class="bg-red-500 px-2 text-white font-bold  rounded mt-2">{{ $message }}</div> @enderror
                                    <div class="flex justify-end space-x-2 mt-2">
                                        <!-- Save (Edit) Button -->
                                        <button type="button" title="Enregistrer les modifications" wire:click.stop='saveEdit'
                                            class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded flex items-center transition duration-150 ease-in-out">
                                            <i class="fas fa-save mr-2"></i>
                                            <span class="mr-2"> Enregistrer </span>
                                        </button>

                                        <!-- Delete Button -->
                                        <button type="button" title="Supprimer le message" wire:click.stop='deleteMessage'
                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded flex items-center">
                                            <i class="fas fa-trash mr-2"></i>
                                            <span class="mr-2">Supprimer</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="flex flex-row w-fit max-w-[100%] break-words">
                                <p class="mr-2 w-fit max-w-[100%] break-words">{!! $messageText !!}</p>
                            </div>
                            @endif
                        </div>
                        @else
                        <div title="{{ $message->updated_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                            class="flex flex-row max-w-xs w-auto p-3 rounded-lg bg-gray-300 text-gray-900">
                            <!-- Signaler -->
                            <button title="Signaler le message"
                                class="share-button flex items-center text-gray-900 dark:text-gray-900 hover:text-orange-400 dark:hover:text-orange-400 mr-2"
                                onclick="event.stopPropagation(); showReportMessageModal({{$message->id}}, 'PrivateMessage');">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                    </svg>
                            </button>
                            <!-- Contenu du message -->
                            <p class="ml-2 w-fit max-w-[100%] break-words">{!! $messageText !!}</p>
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
            @php
                $can_send_messages = true;
                //If the user doesn't want to get messages from people they don't follow and they don't follow you, you can't send messages
                if (!$targetUser->can_get_messages_from_anyone && $targetUser->followed_users()->where('id', Auth::id())->count() == 0)
                    $can_send_messages = false;
            @endphp
            @if ($can_send_messages)
            @error('messageLength') <br><div class="text-red-400 font-bold mt-2">{{ $message }}</div> @enderror
            <form wire:submit.prevent="send" class="flex items-center">
                <!-- Champ de texte pour écrire le message -->
                <input type="text" wire:model="messageContent" placeholder="Écrire un message..." maxlength="2000" minlength="1"
                    class="flex-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"/>
                
                <!-- Bouton d'envoi avec une icône d'avion en papier -->
                <button type="submit" title="Envoyer le message" class="ml-2 p-2 bg-indigo-500 text-white rounded-full hover:bg-indigo-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v6l16-8-16-8v6l10 2-10 2z" />
                    </svg>
                </button>
            </form>
            @else
            <p class="dark:text-gray-200">
                Vous ne pouvez pas envoyer de messages à cette personne
            </p>
            @endif
        </div>                
    </div>
    @else
    <div class="p-4">
        <p class="text-gray-500 dark:text-gray-300">
            Sélectionner un usager pour voir vos conversations
        </p>
    </div>
    @endif

    @script
    <script>
        $wire.on('updateSelectedConversation', () => {
            setTimeout(() => {
                let element = document.querySelector("#message_area");
                if (element && element.children[1].children.length > 2){
                    element.children[1].children[element.children[1].children.length - 1].scrollIntoView();
                }
            }, 100);
            
        });
    </script>
    @endscript
</div>