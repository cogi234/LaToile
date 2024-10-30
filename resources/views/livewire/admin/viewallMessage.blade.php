<?php

use Livewire\Volt\Component;
use App\Models\ReportMessage;

new class extends Component {
    public $reportedMessages;

    public function mount()
    {
        $this->reportedMessages = ReportMessage::where('handled', 0)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();
    }
};

?>

<div>
    <div>
        <h5 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Messages Reportés</h5>

        <div class="ml-4 text-gray-900 dark:text-gray-100 mb-4">
            <div class="flex sm:flex-row flex-col sm:space-y-0 space-y-8 items-center">
                @if($reportedMessages->isEmpty())
                    <p class="text-lg text-gray-700 dark:text-gray-400">Aucun message reporté non traité trouvé.</p>
                @else
                    <ul class="w-full">
                        @foreach($reportedMessages as $message)
                            <div class="border border-gray-300 rounded-lg p-4 mb-4 shadow-sm bg-white dark:bg-gray-800 transition hover:bg-gray-100 dark:hover:bg-gray-700">
                                <li class="flex flex-col">
                                    <span class="text-gray-700 dark:text-gray-300"><strong>Raison:</strong> {{ $message->reason }}</span>
                                    <span class="text-gray-700 dark:text-gray-300"><strong>Type de message:</strong> {{ $message->message_type }}</span>
                                    <span class="text-gray-700 dark:text-gray-300"><strong>Créé le:</strong> {{ $message->created_at->format('Y-m-d H:i:s') }}</span>
                                    <span class="text-gray-700 dark:text-gray-300"><strong>Traité:</strong> {{ $message->handled ? 'Oui' : 'Non' }}</span>
                                </li>
                                <div class="flex flex-row">
                                    <!-- Faux report -->
                                    <livewire:admin.false-report :reportId="$message->id" />

                                    <!-- Avertissement -->
                                    <button title="Marqué le report comme traité et envoyer un avertissement à l'utilisateur"
                                        class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 mr-4"
                                        onclick="event.stopPropagation(); showWarningModal({{ $message->message_id }}, {{ $message->id }}, 'Report');">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                                        </svg>
                                        <span class="ml-1">Envoyer un avertissement</span>
                                    </button>

                                    <!-- Supprimer post -->
                                    <button title="Supprimer le message reporté"
                                        class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 mr-4"
                                        onclick="event.stopPropagation(); showPostDeletePopup({{ $message->message_id }})">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        <span class="ml-1">Supprimer le message</span>
                                    </button>

                                    <!-- Bannir l'utilisateur -->
                                    <button title="Marqué le report comme traité, bannir l'utilisateur et cacher son message"
                                        class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-800 dark:hover:text-red-500 mr-4"
                                        onclick="event.stopPropagation(); showBanUserModal({{ $message->message_id }}, {{ $message->id }}, 'Report');">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L4.25 5.491Z" />
                                        </svg>
                                        <span class="ml-1">Bannir l'utilisateur</span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
