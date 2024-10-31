<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\GroupMessage;

new class extends Component {
    public $groupMessages = [];
    public $groups = [];
    public $selectedGroup = null;
    public ?Group $targetGroup = null;
    public ?int $targetGroupId = null;
    public $selectedConversation = [];
    
    
    public $searchQuery = '';
    public $searchResults = [];

    public $groupMembers = [];

    public string $messageContent = '';
    public $editingMessageId = null;
    public $editMessageContent = '';

    public function mount(?int $targetGroupId)
    {
        $this->updateGroupConversations();

        if ($targetGroupId !== null) {
            $this->setSelectedGroup($targetGroupId);
        }
    }

    public function setSelectedGroup(int $targetGroupId) {
        $targetGroup = Group::find($targetGroupId);

        if (!$targetGroup) { 
            $this->targetGroupId = null;
            $this->targetGroup = null;  
            session()->flash('error', 'Groupe non trouvé');
            return;
        }

        $this->targetGroupId = $targetGroupId;
        $this->targetGroup = $targetGroup;
        $this->getGroupMembers();
        $this->updateSelectedConversation();
        $this->dispatch('updateSelectedConversation');
    }


    public function updateGroupConversations() {
        
        $this->groups = Group::whereHas('memberships', function ($query) {
            $query->where('user_id',  Auth::id());
        })->get();
        

        $this->updateSelectedConversation();
    }

    public function updateSelectedConversation() {
        $this->selectedConversation = GroupMessage::where(function($query) {
            $query->where('user_id', Auth::id())
                ->where('group_id', $this->targetGroupId);
        })->get();
    }
    public function getGroupMembers()
    {
        if ($this->targetGroup) {
            $this->groupMembers = $this->targetGroup->memberships()->get();
        }
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

        GroupMessage::create([
            'message' => trim($this->messageContent),
            'user_id' => Auth::id(),
            'group_id' => $this->targetGroupId,
        ]);

        $this->messageContent = '';

        $this->updateSelectedConversation();
    }

    public function startEditing($messageId)
    {
        $this->editingMessageId = $messageId;
        $message = GroupMessage::find($messageId);
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

        $message = GroupMessage::find($this->editingMessageId);
        if ($message && $message->sender_id == Auth::id()) {
            $message->message = trim($this->editMessageContent);
            $message->save();
        }
        $this->stopEditing();
        
        $this->updateSelectedConversation();
    }

    public function deleteMessage()
    {
        $message = GroupMessage::find($this->editingMessageId);
        if ($message && $message->user_id == Auth::id()) {
            $message->delete();
        }
        
        $this->updateSelectedConversation();
    }
}; ?>

<div wire:click='stopEditing' class="grid grid-cols-2 h-full bg-white dark:bg-gray-800">
    <div class="border-r-2 h-full overflow-y-auto">
        
        <!-- Board pour changer avec messages de groupe + création -->
        <livewire:messages.messageBoard :isGroup='true' wire:key='messageBoardComponent' />
        @if($groups->isEmpty() && $targetGroupId == null)
        <div class="p-4">
            <p class="text-gray-500 dark:text-gray-300">
                Bienvenue à votre messagerie de groupes.
            </p>
        </div>
        @else
            <div>
                <!-- Search Bar -->
                <div class="p-4" >
                    <div class="relative">
                        <input type="text" name="query" id="searchBar"
                            class="block w-full pl-10 pr-4 py-2 bg-gray-200 dark:bg-slate-600 text-gray-900 dark:text-white rounded-full focus:outline-none focus:bg-white focus:text-gray-900 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm"
                            placeholder="Rechercher des groupes"/>
                
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m2.1-6.95a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- List of Groups -->
                <div>
                    @foreach ($groups as $group)
                        <div wire:click='setSelectedGroup({{ $group->id }})' wire:key='groupe_{{ $group->id }}'
                            class="p-4 max-h-[calc(100vh-200px)] hover:bg-gray-200 dark:hover:bg-gray-900 cursor-pointer overflow-y-auto 
                            @if ($group->id == $targetGroupId) bg-gray-100 dark:bg-gray-700 @endif">
                            <div class="flex items-center">
                                {{-- <img class="h-10 w-10 rounded-full" src="{{ $sender->getAvatar() }}" alt="Avatar de {{ $sender->name }}"/> --}}
                                <div class="ml-3 text-sm font-medium text-gray-900 dark:text-white ">
                                    {{ $group->name }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    
    @if ($targetGroup !== null)
        <div id="message_area" class="h-full flex flex-col overflow-y-scroll">
            <!-- Infos de la discussion -->
            <div class="flex items-center gap-5 pl-4 pt-2 w-full">
                {{-- <img class="w-12 h-12 rounded-full shadow-lg" alt="Avatar de {{ $targetUser->name }}" src="{{ $targetUser->getAvatar() }}"/> --}}
                <div class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                    {{ $targetGroup->name }}
                </div>
                <div class="pr-12">
                    <button class="text-gray-500 dark:text-gray-300" onclick="toggleOptionsMenu()">
                        &#x2026;
                    </button>
                
                    <!-- Menu déroulant avec les options -->
                    <div id="optionsMenu" class="absolute bg-white dark:bg-gray-700 shadow-md rounded-lg mt-2 p-4 hidden">
                        <!-- Option pour voir les membres du groupe -->
                        <button type="button" id="viewMembers" onclick="toggleMembersMenu()" class="flex justify-between items-center w-full btn btn-primary py-1 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                            Voir les membres
                            <span class="ml-2">
                                <!-- SVG flèche droite -->
                                <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.707 9.293a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </span>
                        </button>
                        
                        <!-- Séparateur -->
                        <hr class="border-gray-300 dark:border-gray-600 my-2">
                        
                        <!-- Option pour quitter le groupe -->
                        <button onclick="leaveGroup()" class="block text-left w-full py-1 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-600 rounded">
                            Quitter le groupe
                        </button>
                    </div>
                    <!-- Membres du groupe -->
                    <livewire:messages.group-members-form />
                    
                
                    {{-- <!-- Menu pour afficher les membres du groupe -->
                    <div id="membersMenu" class="absolute bg-white dark:bg-gray-700 shadow-md rounded-lg mt-2 p-4 hidden">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Membres</h4>
                        @foreach ($groupMembers as $member)
                            <div class="py-1 text-gray-800 dark:text-gray-300">
                                {{ $member->name }}
                            </div>
                        @endforeach
                    </div> --}}
                    
                </div>
            </div>
            <!-- Zone de discussion -->
            <div id="discussion" class="flex-1 p-4">
                @if ($selectedConversation)
                    @foreach ($selectedConversation as $message)
                        @php
                            $isCurrentUserMessage = $message->user_id == Auth::id();
                            $currentTimeZone = 'America/Toronto';
                            $timeFormat = 'Y-m-d H:i';
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
                                    <p class="mr-2 w-fit max-w-[100%] break-words">{{ $message->message }}</p>
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
                                <p class="ml-2 w-fit max-w-[100%] break-words">{{ $message->message }}</p>
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
            </div>                
        </div>
    @else
        <div class="p-4">
            <p class="text-gray-500 dark:text-gray-300">
                Sélectionner un groupe pour voir vos conversations
            </p>
        </div>
    @endif
    <script>
        function toggleOptionsMenu() {
            document.getElementById("optionsMenu").classList.toggle("hidden");
        }
    
        function leaveGroup() {
            alert('Vous avez quitté le groupe');
        }

        function toggleMembersMenu() {
            this.dispatchEvent(
                new CustomEvent('open-member-menu')
            );
        }
    </script>
</div>