<?php

namespace App\Listeners;

use App\Events\PostDeleting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        $toHandle = $post->eager_direct_shares()->get();

        while($toHandle->count() > 0) {
            $other = $toHandle->shift();
            $shares = $other->eager_direct_shares()->get();
            foreach ($shares as $share) {
                $toHandle->push($share);
            }
            if ($other->previous_content == null)
                continue;

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
            $other->save([ 'timestamps' => false, 'touch' => false ]);
        }
        
        foreach($post->direct_shares as $share){
            $share->previous_id = null; // Détache tous les partages directs
            $share->save([ 'timestamps' => false, 'touch' => false ]);
        }

        foreach($post->shares as $share){
            $share->original_id = null; // Détache tous les partages distants
            $share->save([ 'timestamps' => false, 'touch' => false ]);
        }

        $post->likes()->detach(); // Détache tous les likes
        $post->tags()->detach(); // Détache tous les tags
    }
}
