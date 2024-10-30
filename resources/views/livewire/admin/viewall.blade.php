<?php

use Livewire\Volt\Component;
use App\Models\Post;
use App\Models\Report;
use Livewire\Attributes\On;

new class extends Component {
    public $reports;
    public $moreAvailable = true;

    public function mount()
    {
        $this->reports = Report::where('handled', false)
            ->with(['user', 'post', 'post.user'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->reports->count() == 10;
    }

    public function loadMore()
    {
        if ($this->moreAvailable) {
            $newReports = Report::where('handled', false)
                ->where('id', '<', $this->reports->last()->id)
                ->with(['user', 'post', 'post.user'])
                ->orderBy('id', 'desc')
                ->take(10)
                ->get();

            // Merge the new posts with the existing ones
            $this->reports = $this->reports->concat($newReports);

            // Check if there are more pages to load
            $this->moreAvailable = $newReports->count() == 10;
        }
    }

    #[On('reset-post-views')]
    public function resetPosts()
    {
        $this->reports = Report::where('handled', false)
            ->with(['user', 'post', 'post.user'])
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        // Check if there are more pages to load
        $this->moreAvailable = $this->reports->count() == 10;
    }
};

?>

<div>
    @foreach ($reports as $report)
            <x-report-view :report="$report" wire:key="report-{{ $report->id }}" />
    @endforeach
     
    @if ($moreAvailable)
        <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
              
            Charger plus de reports
        </x-primary-button>
    @else
        <div class="dark:text-gray-300 text-center">Il n'y a plus de reports à traiter.</div>
    @endif
</div>
