

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-100">
    <div class="text-lg font-bold">
        {{ $post->user->name }}
    </div>
    <hr />
    @foreach ($post->content as $block)
        @if ($block['type'] == 'text')
            <p class="p-2 ">
                {{ $block['content'] }}
            </p>
        @endif
    @endforeach
</div>