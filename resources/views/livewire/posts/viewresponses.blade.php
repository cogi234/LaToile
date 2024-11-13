<?php

use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\User;
use Livewire\Attributes\On; 
use Livewire\Attributes\Locked;

new class extends Component {
    #[Locked]
    public $responses;
    #[Locked]
    public $moreAvailable = true;
    #[Locked]
    public $post;

    public function mount(Post $post)
    {
        $this->post = $post;
        $this->responses = $this->post->original_shares()
            ->withCount('tags as tags_count')
            ->where(function ($query) {
                $query->where('tags_count', '!=', 0)
                ->orWhere('content', '!=', '[]');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        dump($this->post->original_shares()
            ->withCount('tags')
            ->where(function ($query) {
                $query->where('tags_count', '!=', 0)
                ->orWhere('content', '!=', '[]');
            }));

        // Check if there are more pages to load
        $this->moreAvailable = $this->responses->count() == 10;
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newResponses = $this->post->original_shares()
                ->withCount('tags')
                ->where(function ($query) {
                    $query->where('tags_count', '!=', 0)
                    ->orWhere('content', '!=', '[]');
                })
                ->where('created_at', '<', $this->responses->last()->created_at)
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            // Merge the new responses with the existing ones
            $this->responses = $this->responses->concat($newResponses);

            // Check if there are more pages to load
            $this->moreAvailable = $newResponses->count() == 10;
        }
    }
    
    #[On('reset-post-views')]
    public function resetresponses()
    {
        $this->responses = $this->post->original_shares()
            ->withCount('tags')
            ->where(function ($query) {
                $query->where('tags_count', '!=', 0)
                ->orWhere('content', '!=', '[]');
            })
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->responses->count() == 10;
    }
};
?>

<!-- Show more button -->
<div>
    @foreach ($responses as $response)
    <x-post-view :post="$response" wire:key='response_{{ $response->id }}' />
    @endforeach


    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de réponses
        </x-primary-button>
    @else
        <div class="dark:text-gray-300 text-center">Il n'y a plus de réponses à voir, revenez plus tard.</div>
    @endif
</div>
