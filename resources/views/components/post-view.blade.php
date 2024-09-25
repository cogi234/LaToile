<div id="post-{{ $post->id }}" onclick="window.location.href = '/post/{{ $post->id }}'" {{ $attributes->merge(['class'
    => "cursor-pointer post bg-white hover:bg-white/50 dark:bg-gray-800 dark:hover:dark:bg-gray-700 overflow-hidden
    shadow-sm rounded-lg mb-4 md:p-5 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0"]) }}>
    <!-- L'utilisateur qui a publier le post -->
    <x-post-user :user="$post->user" :time="$post->created_at" :postId="$post->id" :key="'user' . $post->id"
        :sharedPost="$post->previous" displayEditButtons="{{ true }}" />

    @if ($post->previous_content != null)
    <!-- Le contenu des posts precedents dans la chaine de partage -->
    <x-post-content :content="$post->previous_content" :postId="$post->id" />
    @endif

    <!-- Contenu du post -->
    <div class="post-content ml-4 mt-4 text-gray-900 dark:text-gray-100">
        @if ($post->previous_content != null && $post->content != null)
        <hr class="mb-2" />
        <x-post-user :user="$post->user" :time="$post->created_at" :postId="$post->id" :key="$post->id"
            edited="{{$post->created_at != $post->updated_at}}" displayEditButtons="{{ false }}" />
        @endif
        <x-post-content :content="$post->content" :postId="$post->id" />
    </div>

    <!-- Tags -->
    @if ($post->tags()->count() > 0)
    <div>
        <hr class="mb-3 border-gray-600" />
        @foreach ($post->tags as $tag)
        <a href="/tag/{{ $tag->id }}" target="_blank" onclick="event.stopPropagation()"
            class="p-1 m-1 rounded-md dark:bg-gray-900 dark:text-gray-400">
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
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 mr-4">
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
                onclick="showPostEditor({{$post->id}}); event.stopPropagation()">
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
            <!-- Signaler -->
            <button title="Signaler le post"
                class="share-button flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </button>
        </div>
    </div>
</div>

<div id="editPostModal"
    class="modal hidden fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div
        class="modal-content w-full md:w-6/12 top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button onclick="closeEditPopup()" title="Fermez le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @php
        $contentArray = is_array($post->content) ? $post->content : json_decode($post->content, true);
        $textContent = isset($contentArray[0]['content']) ? $contentArray[0]['content'] : '';
        @endphp
        <form action="{{ route('posts.updatePost', $post->id) }}" method="POST" id="editPostForm">
            @csrf
            @method('PATCH')
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Modifier le post</span>

            <textarea name="newContent" id="postContent" placeholder="Texte du post modifié ici" rows="5" class="w-full p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                    rounded-md shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-20 rounded"
                minlength="5" required>{{ $textContent }}</textarea>
            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeEditPopup()"
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">Annuler</button>
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 hover:bg-gray-700 dark:hover:bg-white dark:bg-gray-200 text-gray-100 rounded text-white dark:text-black transition ease-in-out duration-150">Modifier
                    le post</button>
            </div>
        </form>
    </div>
</div>

<div id="deleteConfirmationModal"
    class="modal hidden fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div
        class="modal-content w-full md:w-6/12 top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button onclick="closeDeleteConfirmationPopup()" title="Fermez le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        @php
        $contentArray = is_array($post->content) ? $post->content : json_decode($post->content, true);
        $textContent = isset($contentArray[0]['content']) ? $contentArray[0]['content'] : '';
        @endphp
        <form action="{{ route('posts.deletePost', $post->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Êtes-vous sûr de vouloir supprimer ce
                post?</span>
            <span class="text-l flex flex-row pb-2 text-black dark:text-white">Celui-ci sera supprimé
                définitivement.</span>

            <div class="flex justify-end mt-4">
                <button type="button" onclick="closeDeleteConfirmationPopup()"
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">Annuler</button>
                <button type="submit"
                    class="px-4 py-2 bg-gray-800 hover:bg-red-600 dark:hover:bg-red-800 dark:bg-gray-200 text-gray-100 rounded text-white dark:text-black transition ease-in-out duration-150">
                    Supprimer définitivement
                </button>
            </div>
        </form>
    </div>
</div>

<x-script-showPostEditor />

<x-script-show-edit-post-popup />