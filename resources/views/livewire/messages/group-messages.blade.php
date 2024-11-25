<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use App\Models\Group;
use App\Models\GroupMessage;
use App\Notifications\MessageReceived;
use Astrotomic\Twemoji\Twemoji;

new class extends Component {
    public $groupMessages = [];
    public $groups = [];
    public $selectedGroup = null;
    public ?Group $targetGroup = null;
    public ?int $targetGroupId = null;
    public $selectedConversation = [];
    public $isCreator = false;


    public $searchQuery = '';
    public $searchResults = [];

    public $groupMembers = [];
    public $invites = [];
    public $onReqOpt = false;

    public string $messageContent = '';
    public $editingMessageId = null;
    public $editMessageContent = '';

    public function mount(?int $targetGroupId)
    {
        $this->updateGroupConversations();
        $this->loadInvitations();
        $this->onReqOpt = false;
        
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

        $this->isCreator = Group::find($targetGroupId)
            ->memberships()
            ->where('user_id', Auth::id())
            ->where('status', 'creator')
            ->exists();

        $this->targetGroupId = $targetGroupId;
        $this->targetGroup = $targetGroup;
        $this->getGroupMembers();
        $this->updateSelectedConversation();
        $this->dispatch('updateSelectedConversation');
    }

    public function switchToChats(){
        if($this->onReqOpt){
            $this->onReqOpt = false;
        }
    }

    public function switchToRequests(){
        if(!$this->onReqOpt){
            $this->onReqOpt = true;
        }
    }

    public function loadInvitations() {
        $this->invites = Group::whereHas('invites', function($query) {
            $query->where('user_id', Auth::id());
        })->get();
    }

    public function acceptInvitation($groupId) {
        $group = Group::find($groupId);
        
        $group->invites()->updateExistingPivot(Auth::id(), ['status' => 'member']);
        
        $this->loadInvitations();
        $this->updateGroupConversations();
    }

    public function rejectInvitation($groupId) {
        Group::find($groupId)->invites()->detach(Auth::id());
        $this->loadInvitations();
    }

    public function leaveGroup($groupId){
        $group = Group::find($groupId);

        if (!$group) {
            return redirect()->back()->with('error', 'Groupe introuvable.');
        }

        $creator_id = $group->memberships()
            ->wherePivot('status', 'creator')
            ->pluck('user_id')
            ->first();
        
        if ($creator_id === Auth::id()) {
            $newCreator = $group->memberships()->where('user_id', '!=', Auth::id())->first();

            if ($newCreator) {
                $group->memberships()
                    ->updateExistingPivot($newCreator->id, ['status' => 'creator']);
            }
        }

        $group->memberships()->detach(Auth::id());

        $remainingMembers = $group->memberships()->count();

        if ($remainingMembers === 0) {
            GroupMessage::where('group_id', $group->id)->delete();

            $group->invites()->detach();

            $group->delete();
        }

        $this->loadInvitations();
        $this->redirect('/messages/group');
    }

    public function updateGroupConversations() {
        
        $this->groups = Group::whereHas('memberships', function ($query) {
            $query->where('user_id',  Auth::id());
        })->get();
        

        $this->updateSelectedConversation();
    }

    public function updateSelectedConversation() {
        $this->selectedConversation = GroupMessage::where(function($query) {
            $query->where('group_id', $this->targetGroupId);
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
        if ($this->editingMessageId === $messageId) {
            $this->editingMessageId = null; // Quittez le mode d'édition si vous êtes déjà en mode édition
        } else {
            $this->editingMessageId = $messageId; // Activez le mode d'édition pour ce message
        }
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
        if ($message && $message->user_id == Auth::id()) {
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
        @if($groups->isEmpty() && $targetGroupId == null && $invites->isEmpty())
        <div class="p-4">
            <p class="text-gray-500 dark:text-gray-300">
                Bienvenue à votre messagerie de groupes.
            </p>
        </div>
        @else
            <div class="md:grid md:grid-cols-10 md:h-screen flex flex-col">
                <!-- Sidebar pour les icônes -->
                <div class="flex flex-row items-center justify-start space-x-4 md:flex-col md:items-center md:space-y-8 md:space-x-0 p-4 border-b md:border-b-0 md:border-r bg-gray-50 dark:bg-gray-900">
                    <!-- Icône pour les messages -->
                    <button 
                        wire:click='switchToChats' 
                        class="text-gray-500 dark:text-gray-300 hover:text-indigo-500 dark:hover:text-indigo-400 
                               @if (!$onReqOpt) text-indigo-500 dark:text-indigo-500 @endif" 
                        title="Messages">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z"/>
                        </svg>                      
                    </button>
            
                    <!-- Icône pour les demandes -->
                    <button 
                        wire:click='switchToRequests' 
                        class="text-gray-500 dark:text-gray-300 hover:text-indigo-500 dark:hover:text-indigo-400 
                               @if ($onReqOpt) text-indigo-500 dark:text-indigo-500 @endif" 
                        title="Demandes">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/>
                        </svg>                      
                    </button>
                </div>
            
                <!-- Section principale -->
                <div class="col-span-9 h-full overflow-hidden bg-white dark:bg-gray-800">
                    @if($onReqOpt)
                        <div class="p-4">
                            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Invitations</h3>
                            @if(count($invites) > 0)
                                <div class="space-y-4">
                                    @foreach($invites as $invite)
                                        <div class="flex justify-between items-center p-3 bg-gray-100 dark:bg-gray-700 rounded shadow">
                                            <span class="text-lg text-gray-900 dark:text-gray-100">{{ $invite->name }}</span>
                                            <div class="space-x-2">
                                                <button 
                                                    wire:click="acceptInvitation({{ $invite->id }})" 
                                                    title="Accepter l'invitation de groupe" 
                                                    class="bg-green-500 hover:bg-green-600 text-white py-1 px-4 rounded">
                                                    Accepter
                                                </button>
                                                <button 
                                                    wire:click="rejectInvitation({{ $invite->id }})" 
                                                    title="Refuser l'invitation de groupe" 
                                                    class="bg-red-500 hover:bg-red-600 text-white py-1 px-4 rounded">
                                                    Refuser
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-600 dark:text-gray-400">Aucune invitation en attente.</p>
                            @endif
                        </div>
                    @else
                        <div class="p-4">
                            <h3 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Groupes</h3>
                            <div class="space-y-2">
                                @foreach ($groups as $group)
                                    <div 
                                        wire:click='setSelectedGroup({{ $group->id }})' 
                                        wire:key='groupe_{{ $group->id }}'
                                        class="p-4 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-900 rounded cursor-pointer 
                                              @if ($group->id == $targetGroupId) border-l-4 border-indigo-500 @endif">
                                        <div class="text-lg text-gray-900 dark:text-white font-medium">
                                            {{ $group->name }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div> 
        @endif
    </div>
    
    @if ($targetGroup !== null)
        <div id="message_area" class="h-full flex flex-col overflow-y-scroll">
            <!-- Infos de la discussion -->
            <div class="flex items-center justify-between pl-4 pt-2 w-full lg:mb-0 mb-3">
                <div class="ml-3 text-sm font-medium text-gray-900 dark:text-white">
                    {{ $targetGroup->name }}
                </div>
                <div class="pr-12">
                    <!-- Menu déroulant avec les options -->
                    <x-dropdown width="w-64">
                        <x-slot name="trigger">
                            <button class="text-gray-500 dark:text-gray-300">
                                &#x2026;
                            </button>
                        </x-slot>
    
                        <x-slot name="content">
                            <!-- Option pour voir les membres du groupe -->
                            <button wire:key='btnMembersMenu' type="button" id="viewMembers" onclick="toggleMembersMenu()" class="flex justify-between items-center w-full btn btn-primary py-1 p-4 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Voir les membres
                                <span class="ml-2">
                                    <!-- SVG flèche droite -->
                                    <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.707 9.293a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </button>
                            @if($isCreator)
                            <button wire:key='btnInvitesMenu' type="button" id="viewInvites" onclick="toggleInvitesMenu()" class="flex justify-between items-center w-full btn btn-primary py-1 p-4 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Voir les invitations
                                <span class="ml-2">
                                    <!-- SVG flèche droite -->
                                    <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.707 9.293a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </button>

                            <button type="button" id="viewMembers" onclick="toggleGroupNameMenu()" class="flex justify-between items-center w-full btn btn-primary py-1 p-4 text-gray-800 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded">
                                Changer le nom du groupe
                                <span class="ml-2">
                                    <!-- SVG flèche droite -->
                                    <svg class="w-4 h-4 text-gray-800 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.707 9.293a1 1 0 010 1.414l-5 5a1 1 0 11-1.414-1.414L11.586 10 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </span>
                            </button>
                            @endif
                            <!-- Séparateur -->
                            <hr class="border-gray-300 dark:border-gray-600 my-2">

                            <!-- Option pour quitter le groupe -->
                            <button wire:click="leaveGroup({{ $targetGroup->id }})" class="block text-left w-full py-1 p-4 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-600 rounded">
                                Quitter le groupe
                            </button>
                        </x-slot>
                    </x-dropdown>
                    
                    <!-- Nom du Groupe -->
                    <livewire:messages.group-name-change :targetGroup="$targetGroup" wire:key='groupName'/>
                    <!-- Membres du groupe -->
                    <livewire:messages.group-members-form :targetGroup="$targetGroup" wire:key='groupMembers'/>
                    <!-- Invitations au groupe -->
                    <livewire:messages.group-invites-form :targetGroup="$targetGroup" wire:key='groupInvites'/>
                </div>
            </div>
            <!-- Zone de discussion -->
            <div id="discussion" class="flex-1 p-4">
                @if ($selectedConversation)
                    @php
                        $lastMessageDate = null; // Variable pour suivre le dernier jour
                    @endphp
                    @foreach ($selectedConversation as $message)
                        @php
                            $isCurrentUserMessage = $message->user_id == Auth::id();
                            $currentTimeZone = 'America/Toronto';
                            $timeFormat = 'Y-m-d H:i';
                            $dateFormat = 'Y-m-d'; // Format pour vérifier les changements de jour
                            $messageDate = $message->created_at->setTimezone($currentTimeZone)->format($dateFormat);

                            $messageText = $message->message;
                            $textWithURLS = preg_replace(
                                '/(https?:\/\/[^\s]+)/',
                                '<a href="$1" target="_blank" rel="noopener noreferrer" class="hover:underline">$1</a>',
                                $messageText
                            );
                            $messageText = Twemoji::text($textWithURLS)->svg()->toHTML();
                        @endphp

                        <!-- Ligne de séparation si une nouvelle journée commence -->
                        @if ($lastMessageDate !== $messageDate)
                            <div class="text-center text-gray-500 my-4">
                                <span>{{ \Carbon\Carbon::parse($message->created_at)->setTimezone('America/Toronto')->locale('fr')->isoFormat('LL') }}</span>
                            </div>
                            @php
                                $lastMessageDate = $messageDate; // Mettre à jour la date du dernier message
                            @endphp
                        @endif

                        <div wire:key='message_{{ $message->id }}' class="p-2 flex {{ $isCurrentUserMessage ? 'justify-end' : 'justify-start' }}">
                            <!-- Contenu du message -->
                            @if($isCurrentUserMessage)
                                <div id="selfMessageContainer" class="flex items-center max-w-[60%] group mr-4">
                                    <!-- Bouton Modifier -->
                                    <div class="hidden group-hover:block max-w-full">
                                        <button type="button" title="Modifier le message" wire:click.stop='startEditing({{ $message->id }})'>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 mr-2 stroke-gray-400 hover:stroke-gray-700 dark:hover:stroke-gray-200">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                            </svg>                              
                                        </button>
                                        <button type="button" title="Copier le message"  onclick="copyToClipboard('messageText_{{ $message->id }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400 hover:stroke-gray-700 dark:hover:stroke-gray-200">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5h10.5M8.25 7.5h10.5m-7.5 6.75H6a2.25 2.25 0 0 1-2.25-2.25V5.25A2.25 2.25 0 0 1 6 3h8.25M6 3V15a2.25 2.25 0 0 0 2.25 2.25h10.5A2.25 2.25 0 0 0 21 15V8.25A2.25 2.25 0 0 0 18.75 6H12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- (Votre contenu existant pour les messages de l'utilisateur actuel) -->
                                    <div title="{{ $message->created_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                                        id="message_{{ $message->id }}" wire:click.stop
                                        class="max-w-full p-3 rounded-lg bg-blue-500 text-white">
                                        @if ($message->id == $editingMessageId)
                                            <!-- Zone d'édition -->
                                            <div>
                                                <div class="mt-2">
                                                    <textarea id="editArea" maxlength="2000" minlength="1"
                                                        wire:model="editMessageContent" 
                                                        wire:keydown.enter="saveEdit" 
                                                        wire:keydown.escape="stopEditing"
                                                        class="p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white h-10 min-h-10 rounded">
                                                    </textarea>
                                                    @error('editMessageLength') <div class="bg-red-500 px-2 text-white font-bold rounded mt-2">{{ $message }}</div> @enderror
                                                    <div class="flex lg:flex-row w-full flex-col justify-end lg:space-x-2 mt-2">
                                                        <!-- Bouton Enregistrer (Éditer) -->
                                                        <button type="button" title="Enregistrer les modifications" wire:click.stop='saveEdit'
                                                            class="px-3 py-1 bg-green-500 hover:bg-green-600 text-white lg:mb-0 mb-2 rounded flex items-center transition duration-150 ease-in-out">
                                                            <i class="fas fa-save mr-2"></i>
                                                            <span class="mr-2"> Enregistrer </span>
                                                        </button>

                                                        <!-- Bouton Supprimer -->
                                                        <button type="button" title="Supprimer le message" wire:click.stop='deleteMessage'
                                                            class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white rounded flex items-center">
                                                            <i class="fas fa-trash mr-2"></i>
                                                            <span class="mr-2">Supprimer</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div id="messageText_{{ $message->id }}" class="hidden">{{ $messageText }}</div>
                                            <!-- (Contenu du message pour l'utilisateur actuel) -->
                                            <div class="w-full break-words">
                                                <p class="break-words">{!! $messageText !!}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <!-- Affichage pour les autres utilisateurs -->
                                <div class="flex items-end max-w-[60%] group">
                                    <div class="mr-3 flex-shrink-0">
                                        <img src="{{ $message->user->getAvatar() }}" alt="Avatar de {{ $message->user->name }}" class="w-10 h-10 rounded-full object-cover shadow-lg">
                                    </div>
                                    <div class="max-w-full">
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ $message->user->name }}</div>
                                        <div title="{{ $message->created_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                                            class="flex flex-nowrap justify-center items-center p-3 rounded-lg bg-gray-300 text-gray-900">
                                            <div class="hidden group-hover:block pl-4">
                                            <!-- Signaler -->
                                            <button title="Signaler le message"
                                                class="share-button flex lg:mb-0 mb-2 items-center text-gray-900 dark:text-gray-900 hover:text-orange-400 dark:hover:text-orange-400 mr-2"
                                                onclick="event.stopPropagation(); showReportMessageModal({{$message->id}}, 'GroupMessage');">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                    stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                    </svg>
                                            </button>
                                            </div>
                                            <div class="break-words w-full pr-4">
                                                <p class="break-words">{!! $messageText !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hidden group-hover:block max-w-full">
                                        <button type="button" title="Copier le message"  onclick="copyToClipboard('messageText_{{ $message->id }}')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 stroke-gray-400 hover:stroke-gray-700 dark:hover:stroke-gray-200">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5h10.5M8.25 7.5h10.5m-7.5 6.75H6a2.25 2.25 0 0 1-2.25-2.25V5.25A2.25 2.25 0 0 1 6 3h8.25M6 3V15a2.25 2.25 0 0 0 2.25 2.25h10.5A2.25 2.25 0 0 0 21 15V8.25A2.25 2.25 0 0 0 18.75 6H12" />
                                            </svg>
                                        </button>
                                        <div id="messageText_{{ $message->id }}" class="hidden">{{ $messageText }}</div>
                                    </div>
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

            {{-- <div id="discussion" class="flex-1 p-4">
                @if ($selectedConversation)
                    @foreach ($selectedConversation as $message)
                        @php
                            $isCurrentUserMessage = $message->user_id == Auth::id();
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
                            <!-- Edit Button -->
                            <button type="button" title="Modifier le message" wire:click.stop='startEditing({{ $message->id }})'>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" class="size-6 mr-2 stroke-gray-400 hover:stroke-gray-700">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>                              
                            </button>
                            <div title="{{ $message->updated_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                                id="message_{{ $message->id }}" wire:click.stop
                                class="lg:max-w-[60%] max-w-[90%] w-auto p-3 rounded-lg bg-blue-500 text-white">

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
                                        <div class="flex lg:flex-row w-full flex-col justify-end lg:space-x-2 mt-2 lg:mb-0 mb-2">
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
                            <div class="flex items-end">
                                <div>
                                    <!-- Avatar de l'usager-->
                                    <div class="mr-3">
                                        <img src="{{ $message->user->getAvatar() }}" alt="Avatar de {{ $message->user->name }}" class="w-10 h-10 rounded-full shadow-lg">
                                    </div>
                                </div>
                                <div>
                                    <div>
                                        <!-- Nom de l'usager-->
                                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ $message->user->name }}</div>
                                    </div>
                                    
                                    <div title="{{ $message->updated_at->setTimezone($currentTimeZone)->format($timeFormat) }}"
                                        class="flex lg:flex-row flex-col lg:max-w-[40%] max-w-[90%] w-auto p-3 rounded-lg bg-gray-300 text-gray-900">
                                        <!-- Signaler -->
                                        <button title="Signaler le message"
                                            class="share-button flex lg:mb-0 mb-2 items-center text-gray-900 dark:text-gray-900 hover:text-orange-400 dark:hover:text-orange-400 mr-2"
                                            onclick="event.stopPropagation(); showReportMessageModal({{$message->id}}, 'PrivateMessage');">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                                </svg>
                                        </button>
                                        <!-- Contenu du message -->
                                        <div class="flex flex-row w-fit max-w-[100%] break-words">
                                            <p class="ml-2 w-fit max-w-[100%] break-words">{!! $messageText !!}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="flex items-center justify-center h-full">
                        <p class="text-gray-500 dark:text-gray-300">Sélectionnez une conversation pour commencer</p>
                    </div>
                @endif
            </div> --}}
            
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

    @script
        <script>
            $wire.on('updateSelectedConversation', () => {
                setTimeout(() => {
                    let element = document.querySelector("#message_area");
                    if (element && element.children[1].children.length > 2) {
                        element.children[1].children[element.children[1].children.length - 1].scrollIntoView();
                    }

                    // const selfMessageContainer = document.querySelector('#selfMessageContainer');
                    // console.log(selfMessageContainer);
                    // if (selfMessageContainer) {
                    //     selfMessageContainer.addEventListener('mouseover', function() {
                    //         selfMessageContainer.classList.add('mr-4');
                    //     });
                    //     selfMessageContainer.addEventListener('mouseleave', function() {
                    //         selfMessageContainer.classList.remove('mr-4');
                    //     });
                    // }
                }, 100);
            });
        </script>
    @endscript
</div>