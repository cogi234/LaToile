@php
$posts = [
(object)[
'user' => (object)['avatar' => 'https://pbs.twimg.com/profile_images/1807322431675326466/GxFhluoM_400x400.jpg', 'name' => 'John Doe'],
'created_at' => now(),
'content' => 'This is a test post content.',
'likes_count' => rand(0, 100),
'comments_count' => rand(0, 50),
'repost_count' => rand(0, 30)
],
(object)[
'user' => (object)['avatar' => 'https://randomuser.me/api/portraits/men/32.jpg', 'name' => 'Alex Martin'],
'created_at' => now()->subHours(2),
'content' => 'Had a great day at the park!',
'likes_count' => rand(0, 100),
'comments_count' => rand(0, 50),
'repost_count' => rand(0, 50)
],
(object)[
'user' => (object)['avatar' => 'https://randomuser.me/api/portraits/women/50.jpg', 'name' => 'Sophia Turner'],
'created_at' => now()->subDays(3)->subHours(4),
'content' => 'Excited for the weekend. Anyone got plans?',
'likes_count' => rand(0, 100),
'comments_count' => rand(0, 50),
'repost_count' => rand(0, 20)
],
];
@endphp



<style>
    .like-button:hover {
        color: #ff6961;
    }
 </style>
<div>
    @foreach($posts as $post)
    <div class="post bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4 md:p-5 sm:p-2 md:mb-5 sm:mb-3 w-full">
        <div class="post-header flex items-center">
            <!-- Image de profil -->
            <img src="{{ $post->user->avatar ?? 'path/to/default/avatar.png' }}" alt="Profile Image" class="w-12 h-12 rounded-full mr-4 shadow-lg">

            <!-- Nom et date -->
            <div>
                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $post->user->name }}</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ strftime('%d %B %Y à %H:%M', strtotime($post->created_at)) }}</p>
            </div>
        </div>

        <!-- Contenu du post -->
        <div class="post-content mt-4 text-gray-900 dark:text-gray-100">
            <p>{{ $post->content }}</p>
        </div>

        <!-- Boutons d'action (J'aime, Reposter, Partager) -->
        <div class="flex justify-between items-center">
            <div class="post-actions mt-4 flex items-center">
                <!-- J'aime -->
                <button title="Aimer" class="like-button flex items-center text-gray-600 dark:text-gray-400 dark:hover:text-red-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                    </svg>
                    <span class="ml-1">{{ $post->likes_count }}</span>
                </button>

                <!-- Commentaire -->
                <button title="Commenter" class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-blue-400 dark:hover:text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 0 1-.923 1.785A5.969 5.969 0 0 0 6 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337Z" />
                    </svg>
                    <span class="ml-1">{{ $post->comments_count }}</span>
                </button>

                <!-- Reposter -->
                <button title="Reposter" class="repost-button flex items-center text-gray-600 dark:text-gray-400 hover:text-green-400 dark:hover:text-green-400 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 0 0-3.7-3.7 48.678 48.678 0 0 0-7.324 0 4.006 4.006 0 0 0-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 0 0 3.7 3.7 48.656 48.656 0 0 0 7.324 0 4.006 4.006 0 0 0 3.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3-3 3" />
                    </svg>
                    <span class="ml-1">{{ $post->repost_count }}</span>
                </button>

                <!-- Partager -->
                <button title="Partager" class="share-button flex items-center text-gray-600 dark:text-gray-400 hover:text-yellow-500 dark:hover:text-yellow-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                    </svg>
                    <span class="ml-1">Partager</span>
                </button>
            </div>
            <div class="post-actions mt-4 flex items-center">
                <!-- Signaler -->
                <button title="Signaler le post" class="share-button flex items-center text-gray-600 dark:text-gray-400 hover:text-red-400 dark:hover:text-red-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    @endforeach
</div>