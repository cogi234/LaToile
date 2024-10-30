<?php

use App\Models\Post;
use App\Models\QueuedPost;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

//Check scheduled posts every minute
Schedule::call(function () {
    $queuedPosts = QueuedPost::whereDate('scheduled_time', '<=', now())->get();
    foreach ($queuedPosts as $queue) {
        //If the shared post id is positive, we are sharing a post. Otherwise, we are creating a new post
        if ($queue->previous != null) {
            $post = $queue->previous->share($queue->user_id, $queue->content);
        } else {
            $post = new Post();
            $post->content = $queue->content;
            $post->user_id = $queue->user_id;
        }

        //We add the post_id to the content blocks
        if ($post->content != null){
            $content = $post->content;
            for ($i = 0; $i < sizeof($content); $i++) {
                $content[$i]['post_id'] = $post->id;
            }
            $post->content = $content;
        }
        
        $post->save();

        //We add the tags
        $post->addTags($queue->tags);

        $queue->delete();
    }
})->everyMinute();