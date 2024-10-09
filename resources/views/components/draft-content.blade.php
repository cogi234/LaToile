<div {{ $attributes->merge(['class' => "ml-4 text-gray-900 dark:text-gray-100"]) }}>
    @foreach ($content as $block)
        @if ($block['type'] == 'text')
            <p class="p-2">
                {{ $block['content'] }}
            </p>
        @endif
    @endforeach
</div>