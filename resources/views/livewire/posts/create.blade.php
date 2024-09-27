<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;

new class extends Component {
    public string $text = "";

    public array $tags = [''];

    #[Locked]
    public array $previousContent = [];
    #[Locked]
    public int $sharedPostId = -1;
    #[Locked]
    public bool $enabled = false;


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

    #[On('open-post-creator')]
    public function open(int $sharedId = -1) {
        $this->sharedPostId = $sharedId;
        $this->enabled = true;
        if ($sharedId >= 0) {
            $previousPost = Post::find($sharedId);

            $this->previousContent = $previousPost->createPreviousContent();
        }
    }

    #[On('close-post-creator')]
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
                $this->addError('tags', 'Un des tag n\'est pas du texte!');
                return;
            }
        }
        
        //Create a content array from the text
        $blocks = Post::parseTextToBlocks($this->text);

        //If the shared post id is positive, we are sharing a post. Otherwise, we are creating a new post
        if ($this->sharedPostId >= 0) {
            $previousPost = Post::find($this->sharedPostId);
            $post = $previousPost->share(Auth::user()->id, $blocks);
        } else {
            $post = new Post;
            $post->content = $blocks;
            $post->user_id = Auth::user()->id;
        }

        //We add the post_id to the content blocks
        if ($post->content != null){
            $content = $post->content;
            for ($i = 0; $i < sizeof($content); $i++) {
                $content[$i]['post_id'] = $post->id;
            }
            $post->content = $content;
        }
        $post->save();

        //We add the tags
        $post->addTags($this->tags);

        $this->dispatch('reset-post-views');

        $this->close();
    }

}; ?>

<div id="post_editor" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50">
    <div class="relative top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">

        <!-- Top command bar -->
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button wire:click='close' title="Fermez le panneau"
                 class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Publier un post</span>
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

    <!-- Script with the function to show the post editor -->
    <script>
        function showPostCreator(postId = -1) {
            //Envoyer l'event pour activer le post editor
            if (postId < 0) {
                this.dispatchEvent(
                    new Event('open-post-creator')
                );
            } else {
                this.dispatchEvent(
                    new CustomEvent('open-post-creator', {
                        detail: {
                            sharedId: postId
                        }
                    })
                );
            }
        }
    </script>
</div>
