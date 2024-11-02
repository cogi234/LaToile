<?php

use Livewire\Volt\Component;
use App\Models\ReportMessage;
use Livewire\Attributes\Locked;

new class extends Component {
    
    #[Locked]
    public int $reportId = -1;

    public function setFalseReport(int $reportId)
    {
        // Mettre à jour le rapport pour indiquer qu'il a été traité
        $report = ReportMessage::find($this->reportId);
        if ($report) {
            $report->handled = 1;
            $report->save();
        }

        return redirect()->route('adminPageMessage');
    }
};
?>

<button wire:click="setFalseReport({{$reportId}})" title="Marqué le report comme traité"
class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-500 dark:hover:blue-green-500 mr-4"
onclick="event.stopPropagation()">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
        stroke="currentColor" class="size-6">
        <path stroke-linecap="round" stroke-linejoin="round"
            d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12" />
    </svg>
    <span class="ml-1">Report traité</span>
</button>