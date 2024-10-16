<?php

use App\Models\Tag;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    public $query;
    public $matchedTags = [];
    public $exactTag = null;

    public function mount($query)
    {
        $this->query = ltrim($query, '#');
        $this->matchedTags = Tag::where('name', 'like', '%' . $this->query . '%')->get();

        // Recherche un tag qui correspond exactement à $query
        $this->exactTag = $this->matchedTags->firstWhere('name',$this->query);
    }
}

?>

<div>
    <div
        class="grid grid-rows-3 grid-flow-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg md:p-5 p-2 md:mb-5 mb-3 w-full">
        @foreach ($matchedTags as $tag)
        <div class="m-4 h-10">
            <a href="/tag/{{ $tag->id }}">
                <div class="hover:underline">
                    #{{ $tag->name }}
                </div>
            </a>
        </div>
        @endforeach
    </div>

    @if($exactTag)
    <div>
        <div class="py-12">
            <div class="mt-6 max-w-5xl mx-auto px-3 sm:px-8">
                <div href="/tag/{{ $exactTag->id }}" target="_blank" onclick="event.stopPropagation()"
                    class="text-6xl text-center p-1 m-1 rounded-md dark:bg-gray-900 dark:text-gray-400">
                    #{{ $exactTag->name }}
                </div>
                @auth
                <div class="text-6xl text-center p-1 m-1 rounded-md dark:bg-gray-900 dark:text-gray-400">
                    <livewire:tags.follow :tagId="$tag->id" :key="$tag->id" />
                </div>
                @endauth

                <div id="all-content" class="content-section" style="display: block;">
                    <livewire:posts.view-specific-tag :tagId="$exactTag->id" />
                </div>
            </div>
        </div>
    </div>
    @endif
</div>