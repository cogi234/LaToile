@php
use Livewire\Volt\Component;
use Astrotomic\Twemoji\Twemoji;

$linkConverter = new class extends Component {
    public function convertUrlToLink($text)
    {
        $textWithURLS = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">$1</a>',
            e($text)
        );

        return Twemoji::text($textWithURLS)->svg()->toHTML();
    }
};

$showMoreButtons = true;
@endphp

<div {{ $attributes->merge(['class' => "ml-4 mt-4 text-gray-900 dark:text-gray-100 relative"]) }} x-data="{ showFullContent: false }" x-init="
    $nextTick(() => {
        if ($refs.postContentPreview.scrollHeight <= $refs.postContentPreview.clientHeight) {
            $refs.showMoreButton.style.display = 'none';
            $refs.fadeEffect.style.display = 'none';
        }
    })">
    <div x-ref="postContent" x-show="showFullContent" class="block" x-cloak>
        @foreach ($content as $block)
            @php
            $post = App\Models\Post::find($block['post_id']);
            if ($post == null) {
                $createdAt = now();
            } else {
                $createdAt = $post->created_at;
            }
            @endphp

            @switch($block['type'])
                @case('user')
                    @php
                    $user = App\Models\User::find($block['id']);
                    @endphp
                    <hr class="mb-2" />
                    <x-post-user :user="$user" :post="$post" displayEditButton="{{ false }}" displayDeleteButton="{{ false }}"
                    :key="$postId . '_' . $block['id'] . '_' . $createdAt" />
                @break
                @case('text')
                    <p class="p-2 w-fit max-w-full break-words cursor-text" onclick="event.stopPropagation()">
                        {!! $linkConverter->convertUrlToLink($block['content']) !!}
                    </p>
                @break
                @case('image')
                    <img src="{{ $block['url'] }}" alt="[Image non fonctionnelle]" class="max-w-full rounded-md mx-auto my-2">
                @break
            @endswitch
        @endforeach
    </div>
    
    <div x-ref="postContentPreview" x-show="!showFullContent" class="block relative" style="max-height: 6.5em; overflow: hidden;">
        @foreach ($content as $block)
            @php
            $post = App\Models\Post::find($block['post_id']);
            if ($post == null) {
                $createdAt = now();
            } else {
                $createdAt = $post->created_at;
            }
            @endphp

            @switch($block['type'])
                @case('user')
                    @php
                    $user = App\Models\User::find($block['id']);
                    @endphp
                    <hr class="mb-2" />
                    <x-post-user :user="$user" :post="$post" displayEditButton="{{ false }}" displayDeleteButton="{{ false }}"
                    :key="$postId . '_' . $block['id'] . '_' . $createdAt" />
                @break
                @case('text')
                    <p class="p-2 w-fit max-w-full break-words cursor-text" onclick="event.stopPropagation()">
                        {!! $linkConverter->convertUrlToLink($block['content']) !!}
                    </p>
                @break
                @case('image')
                    <img src="{{ $block['url'] }}" alt="[Image non fonctionnelle]" class="max-w-full rounded-md mx-auto my-2">
                @break
            @endswitch
        @endforeach
        
        <!-- Fade effect overlay -->
        <div x-ref="fadeEffect" class="fadeEffect absolute bottom-0 left-0 right-0 h-8 bg-gradient-to-t from-white dark:from-gray-800 pointer-events-none z-10"></div>
    </div>

    @if ($showMoreButtons)
    <!-- Full-width button with no background -->
    <button x-show="!showFullContent" onclick="event.stopPropagation()" @click="showFullContent = true" x-ref="showMoreButton" class="text-left w-full text-gray-500 dark:text-gray-400 hover:underline mt-2" x-cloak>
        ... Voir la suite
    </button>
    <button x-show="showFullContent" onclick="event.stopPropagation()" @click="showFullContent = false" class="text-gray-500 dark:text-gray-400 hover:underline mt-2" x-cloak>
        ... Moins
    </button>
    @endif
</div>
