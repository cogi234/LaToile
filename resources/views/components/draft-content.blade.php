@php
use Livewire\Volt\Component;

$linkConverter = new class extends Component {

    public function convertUrlToLink($text)
    {
        return preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">$1</a>',
            e($text)
        );
    }
}
@endphp

<div {{ $attributes->merge(['class' => "ml-4 text-gray-900 dark:text-gray-100"]) }}>
    @foreach ($content as $block)
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
                <img src="{{ $block['url'] }}" alt="[Image non fonctionnelle]" class="max-w-full rounded-md">
            @break
        @endswitch
    @endforeach

    @if (count($content) == 0)
        <i>Il n'y a pas de contenu.</i>
    @endif
</div>