<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Locked;
use App\Models\Post;

new class extends Component {
    #[Locked]
    public int $id;

    #[Validate('required')]
    public array $post_content;

    private function splitParagraphs(string $block_content) : string {
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
    }
    
    public function store() {
        $encoded_content = json_encode(array_filter($post_content, function($block) {
            return strlen(trim($block['content'])) > 0; //We filter out empty blocks
        }));

        if ($this->id < 0) {
            //This is a new post
            $post = new Post;
            $post->content = $encoded_content;
            $post->user_id = Auth::user()->id;
            $post->save();
        } else {
            //We are editing a post
            $post = Post::find($this->id);
            $post->content = $encoded_content;
            $post->save();
        }
        $this->reset();
    }

    public function mount($post = null) {
        $this->id = -1;
        $this->post_content = [ [ 'type' => 'text', 'content' => '' ] ];
        if ($post != null) {
            $this->id = $post->id;
            $this->post_content = json_decode($post->content, true);
        }
    }

}; ?>

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 my-4">
    <form wire:submit='store'>
        <div
            class="block w-full border-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
            style="border:1px"
        >
            @foreach ($post_content as $block)
                @switch($block['type'])
                    @case('text')
                        <textarea 
                            class="block w-full bg-white dark:bg-gray-800" 
                            style="border:0px"
                            wire:model='content.{{$loop->index}}' 
                        @if ($loop->first)
                            placeholder='Partagez vos pensées'
                        @endif
                        ></textarea>
                        @break
                    @case('image')
                        @break
                    @case('video')
                        @break
                    @case('audio')
                        @break
                    @case('poll')
                        @break
                @endswitch
            @endforeach
        </div>
        <x-primary-button class="mt-4">{{ __('Publier') }}</x-primary-button>
    </form>
</div>