    <?php

    use Livewire\Volt\Component;
    use App\Models\Post;
    use App\Models\User;
    use Livewire\Attributes\On; 

    new class extends Component {
        public $posts;
        public $moreAvailable = true;

        public $filterOption = 'newest';

        public function mount()
        {
            //Get the ids of all users we follow
            $followedUserIds = User::with('followed_users')
                ->find(Auth::id())
                ->followed_users()->pluck('id');

            $posts = Post::blockedUserPostCheck()
                ->whereIn('user_id', $followedUserIds)
                ->where('hidden', false);
            
            if ($this->filterOption === 'newest') {
                $posts->orderBy('id', 'desc');
            } else {
                // Utilise une jointure pour compter les likes et trier par le nombre de likes
                $posts->withCount('likes')
                    ->orderBy('likes_count', 'desc')
                    ->orderBy('id', 'desc');
            }

            $this->posts = $posts
                ->take(config('app.posts_per_load', 20))
                ->with(['user', 'tags', 'likes'])
                ->get();

            // Check if there are more pages to load
            $this->moreAvailable = $this->posts->count() == config('app.posts_per_load', 20);
        }

        public function loadMore()
        {
            if ($this->moreAvailable) {
                //Get the ids of all users we follow
                $followedUserIds = User::with('followed_users')
                    ->find(Auth::id())
                    ->followed_users()
                    ->pluck('id');

                $newPosts = Post::blockedUserPostCheck()
                    ->whereIn('user_id', $followedUserIds)
                    ->where('id', '<', $this->posts->last()->id)
                    ->where('hidden', false);
                
                if ($this->filterOption === 'newest') {
                    // Si filtre "newest"
                    $newPosts->orderBy('id', 'desc');
                } else {
                    // Si filtre "popular"
                    $newPosts->withCount('likes')
                        ->orderBy('likes_count', 'desc')
                        ->orderBy('id', 'desc');
                }

                $newPosts = $newPosts
                    ->take(config('app.posts_per_load', 20))
                    ->with(['user', 'tags', 'likes'])
                    ->get();

                // Merge the new posts with the existing ones
                $this->posts = $this->posts->concat($newPosts);

                $this->posts->load(['user', 'tags', 'likes']);

                // Check if there are more pages to load
                $this->moreAvailable = $newPosts->count() == config('app.posts_per_load', 20);
            }
        }

        #[On('reset-post-views')]
        public function resetPosts(){
            //Get the ids of all users we follow
            $followedUserIds = User::with('followed_users')->find(Auth::id())->followed_users()->pluck('id');

            $posts = Post::blockedUserPostCheck()
                ->whereIn('user_id', $followedUserIds)
                ->where('hidden', false);
           
            if ($this->filterOption === 'newest') {
                $posts->orderBy('id', 'desc');
            } else {
                // Utilise une jointure pour compter les likes et trier par le nombre de likes
                $posts->withCount('likes')
                    ->orderBy('likes_count', 'desc')
                    ->orderBy('id', 'desc');
            }
            
            $this->posts = $posts
                ->take(config('app.posts_per_load', 20))
                ->with('user', 'tags', 'likes')
                ->get();

            // Check if there are more pages to load
            $this->moreAvailable = $this->posts->count() == config('app.posts_per_load', 20);
        }

        #[On('set-filter-followedUsers-option')]
        public function setFilterOption($option)
        {
            if ($this->filterOption !== $option) {
                $this->filterOption = $option;
                $this->resetPosts();
            }
        }
    }; ?>

    <!-- Blade Template -->
    <div>
        @foreach ($posts as $post)
            <x-post-view :post="$post" wire:key='post_{{ $post->id }}'>{{ $post->title }}</x-post-view>
        @endforeach

        @if ($moreAvailable)
            <x-primary-button wire:click='loadMore' class="mt-2 mx-auto !bg-slate-500/90 hover:!bg-slate-700/90 dark:!bg-slate-400/50 dark:hover:!bg-slate-500 dark:!text-gray-100 transition duration-300 ease-in-out">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 mr-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                
                Charger plus de posts
            </x-primary-button>
        @else
            <div class="dark:text-gray-300 text-center">Il n'y a plus de post à voir, revenez plus tard.</div>
        @endif

        <script>
            function applyFilterFollowedUsers(filter = 'newest') {
                //Envoyer l'event pour activer le post editor{
                this.dispatchEvent(
                    new CustomEvent('set-filter-followedUsers-option', {
                        detail: {
                            option: filter
                        }
                    })
                );
            }
        </script>
    </div>
