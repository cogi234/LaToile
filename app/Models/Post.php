<?php

namespace App\Models;

use App\Events\PostDeleting;
use App\Notifications\PostShared;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'previous_content',
        'user_id',
        'post_id',
        'original_id',
        'previous_id',
        'hidden'
    ];
    /**
     * Some attributes get cast
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
            'previous_content' => 'array'
        ];
    }

    /**
     * The event map for the model.
     *
     * @var array<string, string>
     */
    protected $dispatchesEvents = [
        'deleting' => PostDeleting::class,
    ];

    //Custom functions

    /**
     * Shares
     * @param int $userId The user who is creating the new post.
     * @param array|null $content The content of the new post. Should be an array in the right format.
     * @return \App\Models\Post
     */
    public function share(int $userId, ?array $content = null): Post
    {
        $newPost = new Post;

        $newPost->content = $content;
        $newPost->user_id = $userId;
        $newPost->previous_id = $this->id;
        $newPost->previous_content = $this->createPreviousContent();
        $newPost->original_id = $this->original_id ?? $this->id;

        $newPost->save();
        
        //Send a notification to the shared user
        User::find($this->user_id)->notify(new PostShared(User::find($userId), $newPost));

        return $newPost;
    }

    public function createPreviousContent(): array
    {
        if ($this->content == null || sizeof($this->content) == 0) {
            //If there's no content, this is a simple share and we just copy the already existing previous content
            return $this->previous_content;
        }

        //If there's content, we combine it with the previous content and a user block
        return array_merge(
            $this->previous_content ?? [],
            [
                [
                    'type' => 'user',
                    'id' => $this->user_id,
                    'post_id' => $this->id
                ]
            ],
            array_map(function($block){
                $block['post_id'] = $this->id;
                return $block;
            }, $this->content)
        );
    }

    public static function parseTextToBlocks(string $text) : array {
        $blocks = [];
        $paragraphs = explode("\n", $text);

        foreach ($paragraphs as $paragraph) {
            if (strlen(trim($paragraph)) > 0) {
                $blocks[] = [
                    'type' => 'text',
                    'content' => $paragraph
                ];
            }
        }

        $filteredBlocks = array_filter($blocks, function($block) {
            return $block['type'] == 'text' && strlen(trim($block['content'])) > 0; //We filter out empty blocks
        });

        return $filteredBlocks;
    }

    public function addTag(string $tagText, bool $indexed = false): void
    {
        $tag = Tag::firstOrCreate([
            'name' => $tagText
        ]);

        $this->tags()->attach($tag);

        //If we want to index the tag, we need to update directly the value, because Eloquent doesn't support modifying pivot values
        if ($indexed) {
            DB::table('post_has_tags')
                ->where('post_id', $this->id)
                ->where('tag_id', $tag->id)
                ->update(['indexed' => true]);
        }
    }

    public function addTags(array $tags): void
    {
        $alreadyAdded = [];
        $indexesLeft = 10;
        foreach ($tags as $tag) {
            $newTag = trim($tag);
            if (strlen($newTag) > 0 && !in_array($newTag, $alreadyAdded)) {
                $alreadyAdded[] = $newTag;
                if ($indexesLeft > 0) {
                    $this->addTag($newTag, true);
                    $indexesLeft--;
                } else {
                    $this->addTag($newTag);
                }
            }
        }
    }

    //Relationships

    /**
     * The user who published this post
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The original post we are a share of/response to
     */
    public function original_post(): BelongsTo
    {
        return $this->belongsTo(Post::class, "original_id", "id");
    }

    /**
     * The posts which are shares of/responses to this post
     */
    public function shares(): HasMany
    {
        return $this->hasMany(Post::class, "original_id", "id");
    }

    /**
     * The posts which are shares of/responses to the original post
     */
    public function original_shares(): HasMany
    {
        //If there's no original id, this is the original
        if ($this->original_id == null)
            return $this->shares();
        //Otherwise, we get the original's shares
        return $this->original_post->shares();
    }

    /**
     * The post we are a share of/response to
     */
    public function previous(): BelongsTo
    {
        return $this->belongsTo(Post::class, "previous_id");
    }

    /**
     * The posts which are direct shares of/responses to this post
     */
    public function direct_shares(): HasMany
    {
        return $this->hasMany(Post::class, "previous_id");
    }
    /**
     * The posts which are direct shares of/responses to this post, eager loaded
     */
    public function eager_direct_shares(): HasMany
    {
        return $this->hasMany(Post::class, "previous_id")->with('eager_direct_shares');
    }

    /**
     * The users that have liked this post
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, "likes");
    }
    public function likeCount(): int
    {
        return $this->likes()->count();
    }

    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * The tags on this post
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "post_has_tags")->withPivot('indexed', 'created_at', 'updated_at');
    }

    /**
     * The tags on this post that are indexed
     */
    public function indexed_tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, "post_has_tags")->withPivot('indexed', 'created_at', 'updated_at')
            ->wherePivot('indexed', true);
    }
    
    /**
     * The reports for this post
     */
    public function reports() : HasMany
    {
        return $this->hasMany(Report::class);
    }
}
