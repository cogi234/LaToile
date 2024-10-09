<div id="draft-{{ $draft->id }}" 
    {{ $attributes->merge(['class' => "cursor-pointer bg-white hover:bg-white/50 dark:bg-gray-800 dark:hover:dark:bg-gray-700 overflow-hidden shadow-sm
    rounded-lg mb-4 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0"]) }}>
    <div class="pl-auto flex flex-row items-start self-start space-x-2">
        <!-- Supprimer -->
        <button title="Supprimer le brouillon" onclick="event.stopPropagation(); showDraftDeletePopup({{$draft->id}});"
            class="ml-auto flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-500">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </button>
    </div>
    
    <!-- Draft content -->
    <x-draft-content :content="$draft->content" />
</div>