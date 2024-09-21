<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;

new class extends Component {
    public string $text = "";

    public Array $tags = [''];

    #[Locked]
    public array $previousContent = [];
    #[Locked]
    public int $sharedPostId = -1;
    #[Locked]
    public bool $enabled = false;


    private function splitParagraphs(string $block_content) : array {
        $blocks = [];
        $paragraphs = explode("\n", $block_content);

        foreach ($paragraphs as $paragraph) {
            if (strlen(trim($paragraph)) > 0) {
                $blocks[] = [
                    'type' => 'text',
                    'content' => $paragraph
                ];
            }
        }

        return $blocks;
    }

    public function updated($property) {
        if (str_starts_with($property, 'tags')) {
            $newTags = [];
            foreach ($this->tags as $tag) {
                $newTag = trim($tag);
                if (strlen($newTag) > 0) {
                    $newTags[] = $newTag;
                }
            }
            $newTags[] = '';
            $this->tags = $newTags;
        }

    }

    #[On('open-post-editor')]
    public function open(int $sharedId = -1) {
        $this->sharedPostId = $sharedId;
        $this->enabled = true;
        if ($sharedId >= 0) {
            $previousPost = Post::find($sharedId);

            //We combine the previous post's previous content, its user and its content to make a new previous_content
            if ($previousPost->content != null){
                $this->previousContent = array_merge(
                    $previousPost->previous_content ?? [],
                    [
                        [
                            "type" => "user",
                            "id" => $previousPost->user->id,
                            "time" => $previousPost->created_at
                        ]
                    ],
                    $previousPost->content
                );
            } else {
                $this->previousContent = $previousPost->previous_content;
            }
        }
    }

    #[On('close-post-editor')]
    public function close(){
        $this->reset('text', 'tags', 'previousContent', 'sharedPostId', 'enabled');
    }
    
    public function store() {
        //Validate
        $this->resetValidation();
        $textLength = strlen($this->text);
        if ($this->sharedPostId < 0 && $textLength == 0) {
            //If we are not sharing a post, we need some text to post
            $this->addError('text', 'Il est impossible de publier un post vide!');
            return;
        }
        if ($textLength > 0 && $textLength < 5) {
            //If we are posting something, the text needs to be at least 5 characters long
            $this->addError('text', 'Il est impossible de publier un post avec moins de 5 caractères!');
            return;
        }
        foreach ($this->tags as $tag) {
            if (!is_string($tag)){
                $this->addError('tags', 'Il est impossible de publier un post avec moins de 5 caractères!');
                return;
            }
        }
        
        $blocks = $this->splitParagraphs($this->text);
        $filtered_blocks = array_filter($blocks, function($block) {
            return strlen(trim($block['content'])) > 0; //We filter out empty blocks
        });
        
        $post = new Post;
        $post->content = $filtered_blocks;
        $post->user_id = Auth::user()->id;
        //If the shared post id is positive, we are sharing a post
        if ($this->sharedPostId >= 0) {
            $previousPost = Post::find($this->sharedPostId);
            //We set the previous_id and previous_content
            $post->previous_id = $this->sharedPostId;
            $post->previous_content = $this->previousContent;
            //If the previous post is part of a chain, we set the original to its original, otherwise, the previous post is the original
            $post->original_id = $previousPost->original_id ?? $previousPost->id;
        }

        $post->save();

        //We add the tags
        $alreadyAdded = [];
        foreach ($this->tags as $tag) {
            $newTag = trim($tag);
            if (strlen($newTag) > 0 && !in_array($newTag, $alreadyAdded)) {
                $alreadyAdded[] = $newTag;
                $post->addTag($newTag);
            }
        }

        $this->close();
    }

}; ?>

<div id="post_editor" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-200/75 dark:bg-gray-900/75">
    <div class="relative top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
        <!-- Top command bar -->
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button wire:click='close'
                 class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Previous content -->
        <x-post-content :content="$previousContent" postId="{{ $this->sharedPostId }}" class="ml-4" />

        <form wire:submit='store'>
            <textarea
                wire:model="text"
                placeholder="Partagez vos pensées"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                    rounded-md shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-20"
            ></textarea>
            @error('text') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror
            <div class="mt-2">
                <p class="text-black dark:text-white">Tags:</p>
                @foreach ($tags as $tag)
                    <span class="m-1 text-gray-800 dark:text-gray-300">#
                    <input type="text" wire:model.blur='tags.{{ $loop->index }}'
                        wire:key='tag_{{ $loop->index }}' maxlength="32" style="width: {{strlen($tag)}}ch"
                        class="inline-block ml-[-3px] py-0 px-1 min-w-10 border-gray-600 focus:border-indigo-300 focus:ring focus:ring-indigo-200 
                        focus:ring-opacity-50 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300"/>
                    </span>
                @endforeach
            </div>
            @error('tags') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror
            <x-primary-button class="mt-2 mx-auto">Publier</x-primary-button>
        </form>
    </div>
</div>
