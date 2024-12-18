<div id="post-{{ $post->id }}" onclick="window.location.href = '/post/{{ $post->id }}'" {{ $attributes->merge(['class'
    => "group cursor-pointer post bg-white hover:bg-gray-50 dark:bg-gray-800 hover:dark:bg-[#2B3544] overflow-hidden
    shadow-sm rounded-lg mb-4 md:p-5 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0"]) }}>
    <!-- L'utilisateur qui a publier le post -->
    <x-post-user
        :key="'user' . $post->id" 
        :user="$post->user" 
        :post="$post"
        displayEditButton="{{ true }}"
        displayDeleteButton="{{ true }}"
        main="true"/>

    <div x-data="{ showFullContent: false, tooTall: true }">
        <div class="block relative" x-cloak x-bind:class="(!showFullContent && tooTall) ? 'overflow-hidden max-h-[200px]' : ''"
            x-init="
                setTimeout(function () {
                    tooTall = $el.clientHeight > 300;
                }, 100);
                $nextTick(() => {
                    tooTall = $el.clientHeight > 300;
                });
            ">
            @if ($post->previous_content != null)
            <!-- Le contenu des posts precedents dans la chaine de partage -->
            <x-post-content :content="$post->previous_content" :postId="$post->id" />
            @endif

            <!-- Contenu du post -->
            <div class="ml-4 text-gray-900 dark:text-gray-100">
                @if ($post->previous_content != null && $post->content != null)
                <hr class="mb-2" />
                <x-post-user 
                :key="$post->id" 
                :user="$post->user" 
                :post="$post"
                displayEditButton="{{ false }}"
                displayDeleteButton="{{ false }}"/>
                @endif
            </div>
            <x-post-content :postId="$post->id" :content="$post->content" />

            <!-- Fade effect overlay -->
            <div x-show="!showFullContent && tooTall" class="absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white group-hover:from-gray-50 dark:from-gray-800 group-hover:dark:from-[#2B3544] pointer-events-none z-10"></div>
        </div>
        <!-- Full-width button with no background -->
        <button x-show="!showFullContent && tooTall" onclick="event.stopPropagation()" @click="showFullContent = true" class="text-gray-500 dark:text-gray-400 hover:underline mt-2" x-cloak>
            ... Voir la suite
        </button>
        <button x-show="showFullContent && tooTall" onclick="event.stopPropagation()" @click="showFullContent = false" class="text-gray-500 dark:text-gray-400 hover:underline" x-cloak>
            ... Moins
        </button>
    </div>

    <!-- Tags -->
    @if ($post->tags()->count() > 0)
    <div>
        <hr class="mb-3 border-none h-[2px] text-gray-200/80 bg-gray-200/80 dark:text-gray-300/15 dark:bg-gray-300/15" />
        @foreach ($post->tags as $tag)
        @php
            $followedTagCSS = "";
            $titleTag = "";
            if (Auth::check() && Auth::user()->followed_tags->contains($tag)) {
                $followedTagCSS = 'border-2 border-green-400 hover:border-green-600';
                $titleTag = 'Vous suivez le tag "' . $tag->name . '"'; 
            }
        @endphp
        <a href="/tag/{{ $tag->id }}" title="{{ $titleTag }}" onclick="event.stopPropagation()"
            class="p-1 m-1 rounded-md bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-900 dark:hover:bg-gray-800 dark:text-gray-400 {{ $followedTagCSS }}">
            #{{ $tag->name }}
        </a>
        @endforeach
    </div>
    @endif

    <!-- Boutons d'action (J'aime, Reposter, Partager) -->
    <div class="flex justify-between items-center">
        <div class="post-actions mt-4 flex items-center">
            <!-- J'aime -->
            <livewire:posts.like id="{{ $post->id }}" :key="'like_' . $post->id" />

            <!-- Commentaire -->
            <button title="Commenter"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 mr-4"
                onclick="showPostCreator({{$post->id}}); event.stopPropagation()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                </svg>
                <span class="ml-1">{{ $post->original_shares->whereNotNull('content')->where('content', '!=',
                    [])->count() }}</span>
            </button>

            <!-- Reposter -->
            <button title="Reposter"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-400 dark:hover:text-green-400 mr-4"
                onclick="showPostCreator({{$post->id}}); event.stopPropagation()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                </svg>
                <span class="ml-1">{{ $post->original_shares->count() }}</span>
            </button>

            <!-- Partager -->
            <button title="Partager" onclick="event.stopPropagation(); copyToClipboard({{$post->id}});"
                class="share-button flex items-center text-gray-600 dark:text-gray-400 hover:text-yellow-500 dark:hover:text-yellow-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                </svg>
                <span class="ml-1">Partager</span>
            </button>
            <div id="clipboardMessage{{$post->id}}"
                class="hidden text-green-500 mt-2 self-center ml-2 transition ease-in-out duration-150">Copié au
                presse-papier !</div>
        </div>
        <div class="post-actions mt-4 flex items-center">
            <!-- À faire plus tard -->
            @if (1 == 0)
            <span class="mr-2 text-gray-600 dark:text-gray-400">Vous avez signalé ce post</span>
            @endif
            <!-- Signaler -->
            @auth
            <button title="Signaler le post"
                class="share-button flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-500"
                onclick="event.stopPropagation(); showReportModal({{$post->id}});">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </button>
            @endauth
        </div>
    </div>
</div>

<script>
    function copyToClipboard(postId) {
        // Génère l'URL du post
        var copyText = location.origin + "/post/" + postId;

        // Vérifiez si l'API clipboard est disponible
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(copyText).then(function() {

                // Afficher le message de succès
                const message = document.getElementById('clipboardMessage' + postId);
                message.classList.remove('hidden');
                message.classList.add('block');

                // Masquer le message après 2 secondes
                setTimeout(function() {
                    message.classList.remove('block');
                    message.classList.add('hidden');
                }, 2000);
            }).catch(function(error) {
                console.error('Erreur lors de la copie :', error);
            });
        }
    }
</script>