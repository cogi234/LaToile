<x-app-layout>
    <div class="py-12">
        <div class="mt-6 max-w-5xl mx-auto px-3 sm:px-8">
            
            <div href="/tag/{{ $tag->id }}" target="_blank" onclick="event.stopPropagation()"
                class="text-6xl text-center p-1 m-1 rounded-md dark:bg-gray-900 dark:text-gray-400">
                #{{ $tag->name }}
            </div>
            <livewire:tags.follow :tagId="$tag->id" :key="$tag->id" />

            <div id="all-content" class="content-section" style="display: block;">
                <livewire:posts.view-specific-tag :tagId="$tag->id" />
                   
            </div>
        </div>
    </div>
</x-app-layout>