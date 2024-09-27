<?php

namespace App\Listeners;

use App\Events\PostDeleting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePostDeletion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PostDeleting $event): void
    {
        $post = $event->post;

        //Delete this post in the previous content of other posts
        $toHandle = $event->post->eager_direct_shares()->get();

        while($toHandle->count() > 0) {
            $other = $toHandle->shift();
            $toHandle->merge($other->eager_direct_shares()->get());
            if ($other->previous_content == null)
                continue;
            
            $updated_at  = $other->updated_at;

            $content = $other->previous_content;
            $newContent = [];
            $id = -2;

            foreach ($content as $block) {
                if ($block['type'] != 'user' && $block['post_id'] == $post->id) {
                    //This block is part of the deleted post
                    if ($id == $block['post_id'])
                        continue;
                    else {
                        $newContent[] = [
                            'type' => 'text',
                            'content' => 'Ce post a été supprimé',
                            'post_id' => -1
                        ];
                        $id = $block['post_id'];
                    }
                } else {
                    $newContent[] = $block;
                }
            }
            $other->previous_content = $newContent;
            $other->updated_at = $updated_at;
            $other->save();
        }
        
        foreach($post->direct_shares as $share){
            $updated_at = $share->updated_at;
            $share->previous_id = null; // Détache tous les partages directs
            $share->updated_at = $updated_at;
            $share->save();
        }

        foreach($post->shares as $share){
            $updated_at = $share->updated_at;
            $share->original_id = null; // Détache tous les partages distants
            $share->updated_at = $updated_at;
            $share->save();
        }

        $post->likes()->detach(); // Détache tous les likes
        $post->tags()->detach(); // Détache tous les tags
    }
}
