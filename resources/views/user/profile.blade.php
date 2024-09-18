<x-app-layout>
    <!-- Post Creation -->
    @auth
    <div class="fixed xl:hidden bg-white dark:bg-gray-800 left-0 top-16 p-[2.1em] w-[100%]">
    </div>
    <button type="button"
        class="fixed left-[50%] translate-x-[-50%] top-[72px] xl:left-5 xl:bottom-5 xl:top-auto xl:translate-x-0 block mx-auto mb-3 md:mb-5 h-12 items-center px-4 py-2 uppercase tracking-widest
            border border-transparent rounded-md font-semibold text-xs bg-gray-800 dark:bg-gray-200 text-white
            dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white
            active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500
            focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
        onclick="showPostEditor()">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3 inline-block">
            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
        </svg>
        Publier un post
    </button>
    <livewire:posts.create />
    @endauth
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-user-view :user="$user"/>
        </div>
    </div>
</x-app-layout>
