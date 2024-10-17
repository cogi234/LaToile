<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Locked;
use App\Models\Post;

new class extends Component {

    public string $text = "";
    
    #[Locked]
    public int $postId = -1;
    #[Locked]
    public bool $enabled = false;
    
    #[On('open-post-editor')]
    public function open(int $postId) {
        //We won't open the popup for a post taht doesn't exist
        $post = Post::find($postId);
        if ($post == null)
            return;

        $this->postId = $postId;
        $this->enabled = true;
        $this->text = $this->unserializeContent($post->content);
    }

    #[On('close-post-editor')]
    public function close(){
        $this->reset('postId', 'enabled', 'text');
    }

    public function updatePost() {
        if (!$this->enabled)
            return;

        $post = Post::find($this->postId);

        //Validate
        $this->resetValidation();
        $textLength = strlen($this->text);
        if ($post->previous_content == null && $textLength == 0) {
            //If we are not sharing a post, we need some text to post
            $this->addError('text', 'Il est impossible de publier un post vide!');
            return;
        }
        if ($textLength > 0 && $textLength < 5) {
            //If we are posting something, the text needs to be at least 5 characters long
            $this->addError('text', 'Il est impossible de publier un post avec moins de 5 caractères!');
            return;
        }
        
        //Create a content array from the text
        $blocks = Post::parseTextToBlocks($this->text);
        
        //We add the post_id to the content blocks
        for ($i = 0; $i < sizeof($blocks); $i++) {
            $blocks[$i]['post_id'] = $post->id;
        }

        //Save the content
        $post->content = $blocks;
        $post->save();

        $this->close();

        $this->dispatch('reset-post-views');
    }

    private function unserializeContent(array $blocks) : string {
        $text = '';

        foreach ($blocks as $block) {
            if ($block['type'] == 'text') {
                if ($text != '')
                    $text .= '\n\n';
                $text .= $block['content']; 
            }
        }

        return $text;
    }
}; ?>

<div class="{{ $enabled ? 'flex' : 'hidden' }} fixed top-0 left-0 w-full h-full bg-gray-900 bg-opacity-50 items-center justify-center overflow-y-scroll">
    <div
        class="md:w-6/12 top-1/4 w-2/4 p-4 pt-2 mx-auto bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
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
        <form wire:submit='updatePost'>
            <span class="text-xl flex flex-row pb-2 text-black dark:text-white">Modifier le post</span>

            <textarea wire:model='text' placeholder="Texte du post modifié ici" 
                class="p-2 block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                    shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-20 rounded"
                minlength="5" required>
            </textarea>
            @error('text') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror
            <div class="flex justify-end mt-4">
                <button type="button" wire:click='close'
                    class="mr-2 px-4 py-2 bg-gray-300 dark:bg-gray-100/50 hover:bg-gray-400 rounded transition ease-in-out duration-150">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-700 dark:hover:bg-white dark:bg-gray-200 rounded text-white
                    dark:text-black transition ease-in-out duration-150">
                    Modifier le post
                </button>
            </div>
        </form>
    </div>
    
    <!-- Script with the function to show the post edit form -->
    <script>
        function showPostEditor(postId = -1) {
            //Envoyer l'event pour activer le post editor{
            this.dispatchEvent(
                new CustomEvent('open-post-editor', {
                    detail: {
                        postId: postId
                    }
                })
            );
        }
    </script>
</div>
