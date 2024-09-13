<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use App\Models\Post;

new class extends Component {
    #[Validate('required', message: "Il est impossible de publier un post vide!")]
    #[Validate('min:3', message: "Il est impossible de publier un post vide!")]
    public string $text = "";

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
    
    public function store() {
        $validated = $this->validate();
        
        $blocks = $this->splitParagraphs($validated['text']);
        $filtered_blocks = array_filter($blocks, function($block) {
            return strlen(trim($block['content'])) > 0; //We filter out empty blocks
        });
        
        $post = new Post;
        $post->content = $filtered_blocks;
        $post->user_id = Auth::user()->id;
        $post->save();

        $this->text = "";
    }

}; ?>

<div>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
        <form wire:submit='store'>
            <textarea
                wire:model="text"
                placeholder="Partagez vos pensées"
                class="block w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm bg-white dark:bg-gray-800 text-black dark:text-white min-h-11"
            ></textarea>
            @error('text') <div class="text-red-600 font-bold mt-2"> {{ $message }}</div> @enderror
            <x-primary-button class="mt-2 mx-auto">Publier</x-primary-button>
        </form>
    </div>
</div>