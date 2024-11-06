<?php

use Illuminate\Support\Carbon;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;
use App\Models\Draft;
use App\Models\QueuedPost;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Config;

new class extends Component {
    use WithFileUploads;

    public array $inputs = [
        [
            'type' => 'text',
            'content' => ''
        ]
    ];

    public array $tags = [''];

    public ?Carbon $queueTime = null;

    #[Locked]
    public array $previousContent = [];
    #[Locked]
    public int $sharedPostId = -1;
    #[Locked]
    public int $draftId = -1;
    #[Locked]
    public int $editId = -1;
    #[Locked]
    public bool $enabled = false;
    #[Locked]
    public bool $enabledQueueDialog = false;


    public function insertTag() {
        $index = sizeof($this->tags) - 1;
        if (mb_strlen(trim($this->tags[$index])) > 0 ) {
            array_splice($this->tags, $index + 1, 0, '');
        }
    }

    public function insertInput($index, $type) {
        switch ($type) {
            case 'text':
                $newInput = ['type' => 'text', 'content' => ''];
                array_splice($this->inputs, $index + 1, 0, [$newInput]);
                $this->dispatch('focus-input', index: $index + 1);
                break;
            case 'image':
                $newInput = ['type' => 'image', 'content' => null];
                $newTextInput = ['type' => 'text', 'content' => ''];
                //We add a new image input. If the next one isn't text, we add text after.
                if (isset($this->inputs[$index + 1]) && $this->inputs[$index + 1]['type'] == 'text')
                    array_splice($this->inputs, $index + 1, 0, [$newInput]);
                else
                    array_splice($this->inputs, $index + 1, 0, [$newInput, $newTextInput]);
                $this->dispatch('focus-input', index: $index + 2);
                //$this->dispatch('click-input', index: $index + 1);
                break;
        }
    }

    public function removeInput($index) {
        //We don't delete the first text element
        if ($this->inputs[$index]['type'] == 'text' && $index == 0)
            return;
        //We always need at least one text after any image or other
        if ($index > 0 && $this->inputs[$index]['type'] == 'text' && $this->inputs[$index - 1]['type'] != 'text')
            return;
        //Delete the relevant input
        array_splice($this->inputs, $index, 1);
        //Focus on the previous input
        if ($index > 0)
        $this->dispatch('focus-input', index: $index - 1);

    }

    public function deleteInTextInput($index) {
        if (!isset($this->inputs[$index]))
            return;
        //If we hit backspace on the last character of a text input
        if ($this->inputs[$index]['content'] == '')
            $this->removeInput($index);
    }

    #[On('open-post-creator')]
    public function open(int $sharedId = -1, int $draftId = -1, int $editId = -1) {
        $this->editId = $editId;
        $this->draftId =  $draftId;
        $this->sharedPostId = $sharedId;
        $this->enabled = true;
        if ($editId >= 0) {
            $original = Post::find($editId);
            $this->inputs = $this->inputsFromContent($original->content);
            $this->tags = $original->tags->map(function ($tag, $key) { return $tag->name; })->toArray();
            if ($original->previous != null) {
                $this->sharedPostId = $original->previous_id;
                $this->previousContent = $original->previous->createPreviousContent();
            }
        } else if ($draftId >= 0) {
            $draft = Draft::find($draftId);
            $this->inputs = $this->inputsFromContent($draft->content);
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

    public function inputsFromContent($content) : array {
        $inputs = [];

        foreach ($content as $block) {
            switch ($block['type']) {
                case 'text':{
                    $inputs[] = [
                        'type' => 'text',
                        'content' => $block['content']
                    ];
                    break;
                }
                case 'image':{
                    if ($inputs == [])
                        $inputs[] = [
                            'type' => 'text',
                            'content' => ''
                        ];
                    $inputs[] = [
                        'type' => 'image',
                        'content' => null,
                        'url' => $block['url']
                    ];
                    break;
                }
                default:
                    break;
            }
        }
        
        //We always end with a text input
        if ($inputs[sizeof($inputs) - 1]['type'] != 'text')
            $inputs[] = [
                'type' => 'text',
                'content' => ''
            ];

        return $inputs;
    }

    public function contentFromInputs($inputs) : array {
        $maxImageSize = Config::get('image.max_image_size');

        $content = [];

        foreach ($inputs as $input) {
            switch ($input['type']) {
                case 'text':{
                    //Split the text into a content array and add it to the existing content
                    array_splice($content, sizeof($content), 0, Post::parseTextToBlocks($input['content']));
                    break;
                }
                case 'image':{
                    $imageBlock = [
                        'type' => 'image'
                    ];
                    // Si une image est téléchargée, la sauvegarder
                    if ($input['content'] != null) {
                        //Essayer de compresser l'image
                        $image = Image::read($input['content'])->scaleDown($maxImageSize, $maxImageSize)->toJpeg();
                        $imageBlock['url'] = '/files/' . Str::random(40) . '.jpg';
                        $image->save('storage' . $imageBlock['url']);
                        $content[] = $imageBlock;
                    }
                    break;
                }
            }
        }

        return $content;
    }

    #[On('close-post-creator')]
    public function close(){
        $this->reset('inputs', 'tags', 'previousContent', 'sharedPostId', 'enabled', 'enabledQueueDialog');
    }
    
    public function publish() {
        if (!$this->validateInputs())
            return;

        if ($this->editId >= 0) {
            $this->editPost();
            return;
        }

        $content = $this->contentFromInputs($this->inputs);

        //If the shared post id is positive, we are sharing a post. Otherwise, we are creating a new post
        if ($this->sharedPostId >= 0) {
            $previousPost = Post::find($this->sharedPostId);
            $post = $previousPost->share(Auth::user()->id, $content);
        } else {
            $post = new Post;
            $post->content = $content;
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
        $content = $this->contentFromInputs($this->inputs);

        //We create a new draft or modify the existing one
        if ($this->draftId >= 0) {
            $draft = Draft::find($this->draftId);
            $draft->content = $content;
            $draft->tags = $this->tags;
        } else {
            $draft = new Draft;
            $draft->user_id = Auth::user()->id;
            $draft->content = $content;
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

    public function editPost() {
        $post = Post::find($this->editId);
        $content = $this->contentFromInputs($this->inputs);

        $post->content = $content;
        //We add the post_id to the content blocks
        if ($post->content != null){
            $content = $post->content;
            for ($i = 0; $i < sizeof($content); $i++) {
                $content[$i]['post_id'] = $post->id;
            }
            $post->content = $content;
        }

        //We remove the tags that were removed from the post, and remove the tags that arent new from our list
        foreach ($post->tags as $tag) {
            $index = array_search($tag->name, $this->tags);
            if ($index === false)
                $tag->detach();
            else
                unset($this->tags[$index]);
        }
        //Then the tags left in our array are the new ones, so we add them
        $post->addTags($this->tags);

        $post->save();
        
        $this->dispatch('reset-post-views');

        $this->close();
    }

    public function openQueueDialog() {
        $this->enabledQueueDialog = true;
    }
    public function closeQueueDialog() {
        $this->enabledQueueDialog = false;
    }

    public function queuePost() {
        if (!$this->validateInputs())
            return;

        //Create a content array from the text
        $content = $this->contentFromInputs($this->inputs);

        //We create a new queued post
        $queuedPost = new QueuedPost;
        $queuedPost->user_id = Auth::user()->id;
        $queuedPost->content = $content;
        $queuedPost->tags = $this->tags;
        $queuedPost->scheduled_time = $this->queueTime;

        //If we are sharing a post, we add its id to the queued post
        if ($this->sharedPostId >= 0) {
            $previousPost = Post::find($this->sharedPostId);
            $queuedPost->previous_id = $previousPost->id;
        }

        $queuedPost->save();

        //If we were using a draft, we delete it
        if ($this->draftId >= 0) {
            $draft = Draft::find($this->draftId);
            $draft->delete();
            $this->dispatch('reset-draft-views');
        }

        $this->close();
    }

    public function validateInputs() : bool {
        $this->resetValidation();

        $textLength = 0;
        $mediaCount = 0;
        foreach ($this->inputs as $input) {
            if ($input['type'] == 'text')
                $textLength += mb_strlen($input['content']);
            else
                $mediaCount++;
        }

        if ($this->sharedPostId < 0 && $textLength == 0 && $mediaCount == 0) {
            //If we are not sharing a post, we need some text to post
            $this->addError('input', 'Il est impossible de publier un post vide!');
            return false;
        }
        if ($textLength > 0 && $textLength < 5 && $mediaCount == 0) {
            //If we are posting something, the text needs to be at least 5 characters long
            $this->addError('input', 'Il est impossible de publier un post avec moins de 5 caractères!');
            return false;
        }
        if ($textLength > 8000) {
            $this->addError('input', 'Il est impossible de publier un post avec plus de 8000 caractères!');
            return false;
        }
        foreach ($this->tags as $tag) {
            if (!is_string($tag)){
                $this->addError('tags', 'Un des tag n\'est pas du texte!');
                return false;
            }
            if (mb_strlen($tag) > 32){
                $this->addError('tags', 'Un des tag est trop long!');
                return false;
            }
        }

        return true;
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
        <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Créer un post</span>
        <!-- Previous content -->
        <x-post-content :content="$previousContent" postId="{{ $this->sharedPostId }}" class="ml-4" />

        <!-- Inputs -->
        <div>
            <div class="flex flex-col rounded-md border-[1px] border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 p-1">
                @foreach ($inputs as $input)
                <div class="group">
                @switch($input['type'])
                    @case('text')
                    <!-- Text input -->
                    <textarea wire:key='input_{{ $loop->index }}' wire:model="inputs.{{ $loop->index }}.content"
                        wire:keydown.enter.prevent='insertInput({{ $loop->index }}, "text")' id="input_{{ $loop->index }}"
                        wire:keydown.backspace='deleteInTextInput({{ $loop->index }})'
                        @if ($loop->first) placeholder="Partagez vos pensées"  autofocus @endif
                        oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'
                        class="block w-full h-10 !border-none !ring-0 resize-none bg-white dark:bg-gray-800 text-black dark:text-white"></textarea>
                    <div class="hidden group-hover:flex group-last:flex flex-row">
                        <button wire:click='insertInput({{ $loop->index }}, "image")' type="button" class="mx-2" title="Ajouter une image">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="size-6 dark:text-gray-100 hover:text-orange-500 dark:hover:text-yellow-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>            
                        </button>
                    </div>
                    @break
                    @case('image')
                    <!-- Image input -->
                    <div class="py-1 relative">
                        <a x-data x-on:click="$refs.fileInput.click()" class="w-fit m-auto block">
                            <input type="file" id="input_{{ $loop->index }}" wire:model="inputs.{{ $loop->index }}.content" x-ref="fileInput" style="display:none">
                            
                            @if ($inputs[$loop->index]['content'] != null)
                                <img src="{{ $inputs[$loop->index]['content']->temporaryUrl() }}" alt="Photo de profil" class="max-w-full">
                            @elseif (isset($inputs[$loop->index]['url']) && $inputs[$loop->index]['url'] != null)
                            <img src="{{ $inputs[$loop->index]['url'] }}" alt="Photo de profil" class="max-w-full">
                            @else
                                <div class="cursor-pointer mx-auto p-2 rounded-md w-fit text-black dark:text-white border-2 border-blue-400">Cliquez ici pour ajouter une image</div>
                            @endif
                        </a>

                        
                        <button wire:click='removeInput({{ $loop->index }})' type="button" title="Enlever l'image"
                            class="absolute float-right top-2 right-2">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                class="size-6 dark:text-gray-100 hover:text-orange-500 dark:hover:text-yellow-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                                      
                        </button>
                    
                        <div wire:loading wire:target="input_{{ $loop->index }}" class="dark:text-gray-100">
                            Chargement...
                        </div>
                    </div>
                    @break
                @endswitch
                </div>
                @endforeach
                        
                <!-- Remove Emoji button stuff while I rework the editor
                <button type="button" id="emoji-button" class="ml-2" title="Émojis">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 dark:text-gray-100 hover:text-orange-500 dark:hover:text-yellow-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
                    </svg>                      
                </button>
                -->
            </div>
            @error('input') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror

            <!-- Tags -->
            <div class="mt-2">
                <p class="text-black dark:text-white">Tags:</p>
                @foreach ($tags as $tag)
                <span class="m-1 text-gray-800 dark:text-gray-300">#
                    <input type="text" wire:model.blur='tags.{{ $loop->index }}' wire:key='tag_{{ $loop->index }}' wire:keydown='insertTag(false)'
                        id="tag_{{ $loop->index }}" maxlength="32" style="min-width: 5em; width: {{ mb_strlen($tag) }}em"
                        class="inline-block ml-[-3px] py-0 px-1 min-w-10 border-gray-600 focus:border-indigo-300 focus:ring focus:ring-indigo-200 
                        focus:ring-opacity-50 rounded-md shadow-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-300" />
                </span>
                @endforeach
            </div>
            @error('tags') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror

            <!-- Buttons -->
            <div class="mt-2">
                <x-primary-button class="mt-2 mx-auto" wire:click='publish'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                    </svg>                      
                    Publier
                </x-primary-button>
                <x-secondary-button class="mt-2 mx-auto ml-2" wire:click='saveDraft'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                    </svg>
                    Sauvegarder un brouillon
                </x-secondary-button>
                <x-secondary-button class="mt-2 mx-auto ml-2" wire:click='openQueueDialog'>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5 mr-2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Planifier la publication
                </x-secondary-button>
            </div>
        </div>
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
        //For the queue time picker
        flatpickr('#date-time-picker', {
            enableTime: true
        });

        //To auto select new tags  
        $wire.on('focus-tag', (event) => {
            setTimeout(() => {
                $('#tag_' + event.index).focus()
            }, 100);
        });

        //To auto select new inputs
        $wire.on('focus-input', (event) => {
            setTimeout(() => {
                $('#input_' + event.index).focus()
            }, 100);
        });
        
        //To auto open new file input dialogs
        $wire.on('click-input', (event) => {
            setTimeout(() => {
                $('#input_' + event.index).click()
            }, 100);
        });
    </script>
    @endscript

    <script>
        //Envoyer l'event pour activer le post editor
        function showPostCreator(postId = -1, draftId = -1, editId = -1) {
            if (editId >= 0) {
                this.dispatchEvent(
                    new CustomEvent('open-post-creator', {
                        detail: {
                            editId: editId
                        }
                    })
                );
            } else if (draftId >= 0) {
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

    <!-- Remove Emoji button stuff while I rework the editor
    <script type="module">
        import { EmojiButton } from 'https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.2/dist/index.js';

        const button = document.querySelector('#emoji-button');
        const textarea = document.querySelector('#postTextArea');
        const picker = new EmojiButton();

        button.addEventListener('click', () => {
            picker.togglePicker(button);
            parseEmoji();
        });

        picker.on('emoji', emoji => {
            textarea.value += emoji.emoji;
            textarea.dispatchEvent(new Event('input'));
        });

        function parseEmoji() {
            if (typeof twemoji !== "undefined" && typeof twemoji.parse === "function") {
                // Parse the document body to replace emoji codes with images
                twemoji.parse(document.body, {
                    base: 'https://cdnjs.cloudflare.com/ajax/libs/twemoji/14.0.2/',
                    folder: '72x72/',
                    ext: '.png'
                });
            } else {
                console.error("Twemoji library did not load correctly.");
            }
        }
    </script>
    -->
</div>