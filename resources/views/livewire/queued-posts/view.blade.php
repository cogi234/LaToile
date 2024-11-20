<?php

use Livewire\Volt\Component;
use App\Models\QueuedPost;
use Livewire\Attributes\On; 
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $queuedPosts;
    public $moreAvailable = true;

    public function mount()
    {
        $this->queuedPosts = QueuedPost::where('user_id', Auth::user()->id)
            ->orderby('id', 'desc')
            ->take(config('app.posts_per_load', 20))
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->queuedPosts->count() == config('app.posts_per_load', 20);
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newQueuedPosts = QueuedPost::where('user_id', Auth::user()->id)
                ->where('id', '<', $this->queuedPosts->last()->id)
                ->orderby('id', 'desc')
                ->take(config('app.posts_per_load', 20))
                ->get();

            // Merge the new queuedPosts with the existing ones
            $this->queuedPosts = $this->queuedPosts->concat($newqueuedPosts);

            // Check if there are more pages to load
            $this->moreAvailable = $newQueuedPosts->count() == config('app.posts_per_load', 20);
        }
    }

    #[On('reset-queue-views')]
    public function resetqueuedPosts()
    {
        $this->queuedPosts = QueuedPost::where('user_id', Auth::user()->id)
            ->orderby('id', 'desc')
            ->take(config('app.posts_per_load', 20))
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->queuedPosts->count() == config('app.posts_per_load', 20);
    }
}; ?>

<div>
    @foreach ($queuedPosts as $queue)
        <x-queue-view :queue="$queue" wire:key='queue_{{ $queue->id }}' />
    @endforeach

    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de brouillons
        </x-primary-button>
    @else
        <div class="dark:text-gray-300 text-center">Il n'y a plus de posts planifiés à voir.</div>
    @endif
</div>