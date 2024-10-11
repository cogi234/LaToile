<?php
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use App\Models\PrivateMessage;
use App\Models\User;

new class extends Component {

    public $messageContent = "";
    public $privateMessages = [];
    public $selectedConversation = [];
    public $targetUser = null;
    public int $targetUserId = 0;
    public int $currentUserId = 0;
    public $uniqueSenderIds = [];

    public function mount(int $targetUserId, int $currentUserId)
    {
        $this->targetUserId = $targetUserId;
        $this->currentUserId = $currentUserId;

        if ($this->targetUserId !== null) {
            $this->targetUser = User::find($this->targetUserId);
            if (!$this->targetUser) {   
                session()->flash('error', 'Utilisateur non trouvé');
                return;
            }
        }

        $this->privateMessages = PrivateMessage::where('sender_id', $this->currentUserId)
            ->orWhere('receiver_id', $this->currentUserId)
            ->get();

        $this->uniqueSenderIds = $this->privateMessages->pluck('receiver_id')->unique()->values()->toArray();

        $this->selectedConversation = PrivateMessage::where(function($query) {
            $query->where('sender_id', $this->currentUserId)
                ->where('receiver_id', $this->targetUserId);
        })->orWhere(function($query) {
            $query->where('sender_id', $this->targetUserId)
                ->where('receiver_id', $this->currentUserId);
        })->get();

        Log::info("Mes messages :" . $this->privateMessages);
        Log::info("Unique senderIds: " . json_encode($this->uniqueSenderIds));
    }


    #[Validate(['messageContent' => 'required|string|max:255'])]
    public function send()
    {
        $this->validate();

        PrivateMessage::create([
            'message' => $this->messageContent,
            'read' => false,
            'sender_id' => $this->currentUserId,
            'receiver_id' => $this->targetUserId,
            'created_at' => now(),
        ]);

        $this->messageContent = '';
        // Reload the page after sending message
        $this->redirect('/messages/' . $this->currentUserId . '-' . $this->targetUserId);
    }
};?>

<x-app-layout>
    <div class="grid grid-cols-2 h-full bg-white dark:bg-gray-800">
        <!-- Conversations List -->
        <div id="conversations" class="border-r-2 h-full overflow-y-auto">
            <!-- Header and Search Bar -->
            <div class="flex flex-row justify-between items-center p-4 bg-gray-100 dark:bg-gray-700">
                <div class="text-xl font-semibold dark:text-white">Messages</div>
                <div id="option" class="text-gray-500 dark:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
            @if($privateMessages->isEmpty() && $targetUserId == null)
                <!-- Message when no private messages are available -->
                <div class="p-4">
                    <p class="text-gray-500 dark:text-gray-300">
                        Bienvenu à votre messagerie. Écrivez entre vous et les autres sur LaToile
                    </p>
                </div>
            @else
                <!-- Search Bar -->
                <div class="p-4" x-data="{ query: '' }">
                    <div class="relative">
                        <input type="text" name="query" id="searchBar" x-model="query"
                            class="block w-full pl-10 pr-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-900 dark:text-white rounded-full focus:outline-none focus:bg-white focus:text-gray-900 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                            placeholder="Rechercher des Messages Directs">
                
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
                @if($privateMessages->isEmpty() && $targetUserId != null)
                    <div>
                        <div class="p-4 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                            <div class="flex items-start">
                                <!-- Avatar à gauche, centré horizontalement -->
                                <div id="avatar">
                                    <img class="w-12 h-12 rounded-full mr-4 shadow-lg" alt="Profile Image"
                                        src="{{ $targetUser->getAvatar() }}">
                                </div>
                        
                                <!-- Nom à droite, aligné en haut et centré horizontalement -->
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
                        {{-- @foreach ($privateMessages as $privateMessage)
                            <div class="p-4 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
                                <a href="{{ url('messages/' . Auth::id() . '-' . $privateMessage->user_id) }}">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <!-- Display user avatar if available -->
                                            <img class="h-10 w-10 rounded-full"
                                                src="{{ $privateMessage->user->avatar_url ?? 'default-avatar.png' }}" alt="">
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $privateMessage->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-300">
                                                {{ Str::limit($privateMessage->last_message, 40) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach --}}
                    </div>
                @endif
            @endif
        </div>

        <!-- Private Message Area -->
        @if($targetUser != null)
            <div id="privateMessage" class="h-full flex flex-col">
                <div id="infoDiscussion" class="flex items-center pl-4 pt-2">
                    <div id="avatar">
                        <img class="w-12 h-12 rounded-full mr-4 shadow-lg" alt="Profile Image"
                            src="{{ $targetUser->getAvatar() }}">
                    </div>
            
                    <!-- Nom à droite, aligné en haut et centré horizontalement -->
                    <div id="Name">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $targetUser->name }}
                        </p>
                    </div>
                </div>
                <!-- Zone de discussion -->
                <div id="discussion" class="flex-1 p-4 overflow-y-auto">
                    {{-- Affiche ici les messages de la conversation sélectionnée --}}
                    @if ($selectedConversation)
                        {{-- @foreach ($selectedConversation as $message)
                            <div class="p-2">
                                <p><strong>{{ $message->sender->name }}:</strong> {{ $message->content }}</p>
                            </div>
                        @endforeach --}}
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
                            class="flex-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        
                        <!-- Bouton d'envoi avec une icône d'avion en papier -->
                        <button type="submit" class="ml-2 p-2 bg-indigo-500 text-white rounded-full hover:bg-indigo-600 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v6l16-8-16-8v6l10 2-10 2z" />
                            </svg>
                        </button>
                    </form>
                </div>                
            </div>
        @endif
    </div>
</x-app-layout>

<style>
    main{
        height: 100vh;
    }
</style> 