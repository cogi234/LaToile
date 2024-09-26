

<div {{ $attributes->merge(['class' => "post-content ml-4 mt-4 text-gray-900 dark:text-gray-100"]) }}>
    @foreach ($content as $block)
        @if ($block['type'] == 'text')
            <p class="p-2 ">
                {{ $block['content'] }}
            </p>
        @elseif ($block['type'] == 'user')
            @php
                $user = App\Models\User::find($block['id']);
            @endphp
                <hr class="mb-2" />
                <x-post-user :user="$user" :time="$block['time']" :postId="$block['post_id']" :postContent="$block['post_content']" :isEdited="0" displayEditButton="{{ false }}" :key="$postId . '_' . $block['id'] . '_' . $block['time']" />
        @endif
    @endforeach
</div>