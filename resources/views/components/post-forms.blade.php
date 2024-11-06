

<div>
    <div class="fixed sm:hidden bg-white dark:bg-gray-800 left-0 top-16 p-[2.1em] w-[100%]">
    </div>
    <button type="button" class="fixed left-[50%] translate-x-[-50%] top-[72px] sm:left-5 sm:bottom-5 sm:top-auto sm:translate-x-0 block mx-auto mb-3 md:mb-5 h-12 items-center px-4 py-2 uppercase tracking-widest
            border border-transparent rounded-md font-semibold text-xs bg-gray-800 dark:bg-gray-200 text-white
            dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white
            active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500
            focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        onclick="showPostCreator()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="size-6 mr-3 inline-block">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
        </svg>

        Créer un post
    </button>
    
    <livewire:posts.create />
    <livewire:posts.delete />
    <livewire:posts.report />
    <livewire:admin.bans />
    <livewire:admin.warnings />
    <livewire:admin.delete-message />
</div>