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
                        @foreach ($privateMessages as $privateMessage)
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
                        @endforeach
                    </div>
                @endif
            @endif
        </div>

        <!-- Private Message Area -->
        <div id="privateMessage" class="h-full overflow-y-auto">
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
            <div id="dicussion">
                    
            </div>
            <div id="messageBar">
                    
            </div>
            {{-- @if ($selectedConversation)
                @foreach ($selectedConversation as $message)
                    <div class="p-4">
                        <p><strong>{{ $message->sender->name }}:</strong> {{ $message->content }}</p>
                    </div>
                @endforeach
            @else
                <div class="flex items-center justify-center h-full">
                    <p class="text-gray-500 dark:text-gray-300">Sélectionnez une conversation pour commencer</p>
                </div>
            @endif --}}
        </div>
    </div>
</x-app-layout>


<script>

</script>