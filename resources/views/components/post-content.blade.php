<div {{ $attributes->merge(['class' => "post-content ml-4 mt-4 text-gray-900 dark:text-gray-100"]) }}>
    @foreach ($content as $block)
        @php
        $post = App\Models\Post::find($block['post_id']);
        if ($post == null) {
            $createdAt = now();
        } else {
            $createdAt = $post->created_at;
        }
        @endphp
        @if ($block['type'] == 'text')
            <p class="p-2 w-fit max-w-[100%] break-words cursor-text" onclick="event.stopPropagation()">
                {{ $block['content'] }}
            </p>
        @elseif ($block['type'] == 'user')
            @php
            $user = App\Models\User::find($block['id']);
            @endphp
            <hr class="mb-2" />
            <x-post-user :user="$user" :post="$post" displayEditButton="{{ false }}" displayDeleteButton="{{ false }}"
                :key="$postId . '_' . $block['id'] . '_' . $createdAt" />
        @endif
    @endforeach
</div>