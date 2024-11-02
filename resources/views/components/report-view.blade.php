
<div id="report-{{ $report->id }}" onclick="window.location.href = '/post/{{ $report->post_id }}'" 
    {{ $attributes->merge(['class' => "cursor-pointer post bg-white hover:bg-white/50 dark:bg-gray-800 dark:hover:bg-gray-700 overflow-hidden
    shadow-sm rounded-lg mb-4 md:p-5 p-2 md:mb-5 mb-3 w-full mt-5 xl:mt-0"]) }}>
    <!-- admin table -->
    <div class="ml-4 text-gray-900 dark:text-gray-100 mb-4">
        <!-- Personne qui a créer le report -->
        <div class="flex sm:flex-row sm:mb-0 mb-4 flex-col items-center">
            <span class="font-bold text-lg mr-4">Reporté par : </span>
            <a href="/user/{{$report->user_id}}" onclick="event.stopPropagation()"
                class="flex flex-row mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-gray-400 transition duration-150 ease-in-out">
                <img src="{{ $report->user->getAvatar() }}" alt="Profile Image"
                    class="w-8 h-8 rounded-full mr-2 shadow-lg hover:outline hover:outline-2 hover:outline-black/10">
                <span
                    class="mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 hover:underline dark:text-white dark:hover:text-gray-400 transition duration-150 ease-in-out">
                    {{ $report->user->name }}
                </span>
            </a>
        </div>
        <!-- Personne reporté -->
        <div class="flex sm:flex-row sm:mb-0 mb-4 flex-col items-center">
            <span class="font-bold text-lg mr-4">Propriétaire du post : </span>
            <a href="/user/{{$report->post->user_id}}" onclick="event.stopPropagation()"
                class="flex flex-row mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 dark:text-white dark:hover:text-gray-400 mb-2 transition duration-150 ease-in-out">
                <img src="{{ $report->post->user->getAvatar() }}" alt="Profile Image"
                    class="w-8 h-8 rounded-full mr-2 shadow-lg hover:outline hover:outline-2 hover:outline-black/10">
                <span
                    class="mr-2 text-lg font-bold text-gray-700 hover:text-gray-900 hover:underline dark:text-white dark:hover:text-gray-400 transition duration-150 ease-in-out">
                    {{ $report->post->user->name }}
                </span>
            </a>
        </div>
        <div class="flex sm:flex-row flex-col sm:text-base text-lg items-center sm:mb-4 cursor-text mb-6" onclick="event.stopPropagation()">
            <strong class="mr-1">Raison du report : </strong> {{ $report->reason }}
        </div>
        @php
            Carbon\Carbon::setLocale('fr');
            $date = $post->reports_date ? Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $post->reports_date) : null;
        @endphp
        <div class="flex sm:flex-row flex-col sm:text-base text-lg items-center sm:mb-4 cursor-text mb-6" onclick="event.stopPropagation()">
            <strong class="mr-1">Reporté le : </strong> {{ $date->translatedFormat('d F Y \à H:i') }}
        </div>
        <div class="flex sm:flex-row flex-col sm:space-y-0 space-y-8 items-center">

            <!-- Cacher un post -->
            <livewire:admin.hide-post id="{{ $report->post_id }}" :key="'hide_' . $report->id"/>
            <!-- Faux report -->
            <livewire:admin.false-report :reportId="$report->id" :key="'treat' . $report->id"/> 

             <!-- Avertissement -->
            <button title="Marquer le report comme traité et envoyer un avertissement à l'utilisateur"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-orange-500 dark:hover:text-orange-400 mr-4"
                onclick="event.stopPropagation(); showWarningModal({{$report->post->user->id}}, {{$report->id}}, {{$report->post_id}}, 'Report');">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.795 3m.38 1.125a23.91 23.91 0 0 1 1.014 5.395m-1.014 8.855c-.118.38-.245.754-.38 1.125m.38-1.125a23.91 23.91 0 0 0 1.014-5.395m0-3.46c.495.413.811 1.035.811 1.73 0 .695-.316 1.317-.811 1.73m0-3.46a24.347 24.347 0 0 1 0 3.46" />
                </svg>                  
                <span class="ml-1">Envoyer un avertissement</span>
            </button>

            <!-- Supprimer post -->
            <button title="Supprimer le post"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-500 dark:hover:text-red-400 mr-4"
                onclick="event.stopPropagation(); showPostDeletePopup({{$report->post->id}})">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                <span class="ml-1">Supprimer le Post</span>
            </button>
            
            <!-- Bannir/Debannir l'utilisateur -->
            @if ($report->post->user->isBanned())
            <livewire:admin.unban :user="$report->post->user" :key="'unban_' . $report->id"/>
            @else 
            <button title="Marqué le report comme traité bannir l'utilisateur et cacher son post"
                class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-800 dark:hover:text-red-500 mr-4"
                onclick="event.stopPropagation(); showBanUserModal({{$report->post->user_id}}, {{$report->id}}, {{$report->post_id}}, 'Report');">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                </svg>
                <span class="ml-1">Bannir l'utilisateur</span>
            </button>
            @endif

        </div>
    </div>

    <!-- Le contenu des posts précédents dans la chaîne de partage -->
    @if ($report->post->previous_content != null)
    <x-post-content :content="$report->post->previous_content" :postId="$report->post->id" />
    @endif

    <!-- Contenu du post -->
    <div class="ml-4 text-gray-900 dark:text-gray-100">
        <x-post-user :key="'post' . $report->post->id" :user="$report->post->user" :post="$report->post" displayEditButton="{{ false }}"
            displayDeleteButton="{{ false }}" />
    </div>

    <x-post-content :postId="$report->post->id" :content="$report->post->content" />

    <!-- Tags -->
    @if (count($report->post->tags) > 0)
    <div>
        <hr class="mb-3 border-gray-600" />
        @foreach ($report->post->tags as $tag)
        <a href="/tag/{{ $tag->id }}" target="_blank" onclick="event.stopPropagation()"
            class="p-1 m-1 rounded-md dark:bg-gray-900 dark:text-gray-400">
            #{{ $tag->name }}
        </a>
        @endforeach
    </div>
    @endif
</div>