<div id="post-{{ $post->id }}" onclick="window.location.href = '/post/{{ $post->id }}'" {{ $attributes->merge(['class'
    => "cursor-pointer post bg-white hover:bg-white/50 dark:bg-gray-800 dark:hover:bg-gray-700 overflow-hidden shadow-sm
    rounded-lg mb-4 md:p-5 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0"]) }}>

    <!-- admin table -->
    <div class="ml-4 text-gray-900 dark:text-gray-100">
        <!-- reporter par qui? -->
        <div class="flex flex-row items-center">
            <a href="/user/{{$post->reporter_id}}" onclick="event.stopPropagation()"
                class="mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-gray-400 transition duration-150 ease-in-out">
                <strong>Reporté par : </strong> {{ $post->reporter_name }}
            </a>
        </div>
        <!-- reporter qui -->
        <div class="flex flex-row items-center">
            <a href="/user/{{$post->owner_id}}" onclick="event.stopPropagation()"
                class="mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-gray-400 transition duration-150 ease-in-out">
                <strong>Propriétaire du post : </strong> {{ $post->owner_name }}
            </a>
        </div>
        <div class="flex flex-row items-center">
            <strong class="mr-1">Raison du report : </strong> {{ $post->reports_reason }}
        </div>
        <div class="flex flex-row items-center">
            <button title="Reposter"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-400 dark:hover:text-green-400 mr-4" onclick="event.stopPropagation()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                  </svg>                                
                <span class="ml-1">Cacher Post</span>
            </button>
            <button title="Reposter"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-400 dark:hover:text-green-400 mr-4" onclick="event.stopPropagation()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                  </svg>                  
                <span class="ml-1">Supprimer Post</span>
            </button>
            <button title="Reposter"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-400 dark:hover:text-green-400 mr-4" onclick="event.stopPropagation()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                  </svg>                  
                <span class="ml-1">Bannir Utilisateur</span>
            </button>
        </div>
        <br>

    </div>

    <!-- Le contenu des posts précédents dans la chaîne de partage -->
    @if ($post->previous_content != null)
    <x-post-content :content="$post->previous_content" :postId="$post->id" />
    @endif

    <!-- Contenu du post -->
    <div class="ml-4 text-gray-900 dark:text-gray-100">
        <x-post-user :key="'post' . $post->id" :user="$post->user" :post="$post" displayEditButton="{{ false }}"
            displayDeleteButton="{{ false }}" />
    </div>

    <x-post-content :postId="$post->id" :content="$post->content" />

    <!-- Tags -->
    @if (count($post->tags) > 0)
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

</div>