<?php

use Illuminate\Support\Carbon;
use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;
use App\Models\Draft;
use App\Models\QueuedPost;

new class extends Component {
    public string $text = "";

    public array $tags = ['', ''];

    public ?Carbon $queueTime = null;

    #[Locked]
    public array $previousContent = [];
    #[Locked]
    public int $sharedPostId = -1;
    #[Locked]
    public int $draftId = -1;
    #[Locked]
    public bool $enabled = false;
    #[Locked]
    public bool $enabledQueueDialog = false;


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
            $newTags[] = '';
            $this->tags = $newTags;
        }

    }

    #[On('open-post-creator')]
    public function open(int $sharedId = -1, int $draftId = -1) {
        $this->draftId =  $draftId;
        $this->sharedPostId = $sharedId;
        $this->enabled = true;
        if ($draftId >= 0) {
            $draft = Draft::find($draftId);
            $this->text = implode('\n\n', array_map(fn($block) => $block['content'], $draft->content));
            $this->tags = $draft->tags;
            if ($draft->previous != null) {
                $this->sharedPostId = $draft->previous_id;
                $this->previousContent = $draft->previous->createPreviousContent();
            }
        } else if ($sharedId >= 0) {
            $previousPost = Post::find($sharedId);

            $this->previousContent = $previousPost->createPreviousContent();
        }
    }

    #[On('close-post-creator')]
    public function close(){
        $this->reset('text', 'tags', 'previousContent', 'sharedPostId', 'enabled', 'enabledQueueDialog');
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

        //If we were using a draft, we delete it
        if ($this->draftId >= 0) {
            $draft = Draft::find($this->draftId);
            $draft->delete();
            $this->dispatch('reset-draft-views');
        }

        $this->dispatch('reset-post-views');

        $this->close();
    }

    public function saveDraft() {        
        //Create a content array from the text
        $blocks = Post::parseTextToBlocks($this->text);

        //We create a new draft or modify the existing one
        if ($this->draftId >= 0) {
            $draft = Draft::find($this->draftId);
            $draft->content = $blocks;
            $draft->tags = $this->tags;
        } else {
            $draft = new Draft;
            $draft->user_id = Auth::user()->id;
            $draft->content = $blocks;
            $draft->tags = $this->tags;

            //If we are sharing a post, we add its id to the draft
            if ($this->sharedPostId >= 0) {
                $previousPost = Post::find($this->sharedPostId);
                $draft->previous_id = $previousPost->id;
            }
        }

        $draft->save();

        $this->close();
    }

    public function openQueueDialog() {
        $this->enabledQueueDialog = true;
    }
    public function closeQueueDialog() {
        $this->enabledQueueDialog = false;
    }

    public function queuePost() {
        $this->resetValidation();
        $textLength = strlen($this->text);
        if ($textLength == 0) {
            //If we are not sharing a post, we need some text to post
            $this->addError('text', 'Il est impossible de publier un post vide!');
            closeQueueDialog();
            return;
        }
        if ($this->queueTime == null || $this->queueTime <= now()) {
            $this->addError('time', 'Il faut choisir un temps de publication qui est dans le futur!');
            return;
        }

        //Create a content array from the text
        $blocks = Post::parseTextToBlocks($this->text);

        //We create a new queued post
        $queuedPost = new QueuedPost;
        $queuedPost->user_id = Auth::user()->id;
        $queuedPost->content = $blocks;
        $queuedPost->tags = $this->tags;
        $queuedPost->scheduled_time = $this->queueTime;

        //If we are sharing a post, we add its id to the queued post
        if ($this->sharedPostId >= 0) {
            $previousPost = Post::find($this->sharedPostId);
            $queuedPost->previous_id = $previousPost->id;
        }

        $queuedPost->save();

        $this->close();
    }

}; ?>

<div id="post_editor" class="
    @if ($enabled)
        fixed
    @else
        hidden
    @endif inset-0 bg-gray-900 bg-opacity-50 overflow-y-scroll">
    <div
        class="relative top-1/4 w-full md:w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">

        <!-- Top command bar -->
        <div class="flex flex-row-reverse pb-2">
            <!-- Close button -->
            <button wire:click='close' title="Fermez le panneau"
                class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Publier un post</span>
        <!-- Previous content -->
        <x-post-content :content="$previousContent" postId="{{ $this->sharedPostId }}" class="ml-4" />

        <form wire:submit='store'>
            <textarea wire:model="text" placeholder="Partagez vos pensées" class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                    rounded-md shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-20"></textarea>
            @error('text') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror
            <div class="mt-2">
                <p class="text-black dark:text-white">Tags:</p>
                @foreach ($tags as $tag)
                <span class="m-1 text-gray-800 dark:text-gray-300">#
                    <input type="text" wire:model.blur='tags.{{ $loop->index }}' wire:key='tag_{{ $loop->index }}'
                        maxlength="32" style="min-width: 5em; width: {{ strlen($tag) }}em"
                        class="inline-block ml-[-3px] py-0 px-1 min-w-10 border-gray-600 focus:border-indigo-300 focus:ring focus:ring-indigo-200 
                        focus:ring-opacity-50 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300" />
                </span>
                @endforeach
            </div>
            @error('tags') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror
            <div>
                <x-primary-button class="mt-2 mx-auto">Publier</x-primary-button>
                <x-secondary-button class="mt-2 mx-auto ml-2" wire:click='saveDraft'>Sauvegarder un brouillon
                </x-secondary-button>
                <x-secondary-button class="mt-2 mx-auto ml-2" wire:click='openQueueDialog'>Planifier la publication
                </x-secondary-button>
            </div>
        </form>
    </div>

    <!-- Queue popup dialog -->
    <div
        class="{{ $enabledQueueDialog ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
        <div
            class="md:w-6/12 top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
            <div class="flex flex-row-reverse pb-2">
                <!-- Close button -->
                <button wire:click='closeQueueDialog' title="Fermez le panneau"
                    class="ml-2 flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="inline">
                <p class="text-xl text-center flex flex-row pb-2 text-black dark:text-white">
                    Choisissez quand le post sera publié
                </p>

                <input class="p-2 rounded-md cursor-pointer dark:bg-gray-100/90 bg-gray-200 dark:bg-gray-300" wire:model='queueTime'
                    id="date-time-picker" type="datetime" placeholder="Planifier une date... "
                />
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="absolute left-[36.5%] top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-500 pointer-events-none">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                @error('time') <div class="text-red-600 font-bold mt-2">{{ $message }}</div> @enderror

                <div class="flex justify-center mt-4">
                    <button type="button" wire:click='queuePost'
                        class="px-4 py-2 mx-2 bg-gray-800 dark:bg-gray-200 hover:bg-gray-700 dark:hover:bg-white rounded text-white dark:text-black transition ease-in-out duration-150">
                        Planifier le post
                    </button>
                    <button type="button" wire:click='closeQueueDialog'
                        class="px-4 py-2 mx-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        flatpickr('#date-time-picker', {
            enableTime: true
        });
    </script>
    @endscript

    <!-- Script with the function to show the post editor -->
    @script
    <script>
        function showPostCreator(postId = -1, draftId = -1) {
            //Envoyer l'event pour activer le post editor
            if (draftId >= 0) {
                this.dispatchEvent(
                    new CustomEvent('open-post-creator', {
                        detail: {
                            draftId: draftId
                        }
                    })
                );
            } else if (postId < 0) {
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
    @endscript
</div>