<?php

use App\Models\Post;
use App\Models\QueuedPost;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    Log::info("Processing queued posts...");
    $queuedPosts = QueuedPost::where('scheduled_time', '<=', now())->get();
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

        $post->created_at = $queue->scheduled_time;
        $post->updated_at = $queue->scheduled_time;
        $post->save();

        //We add the tags
        $post->addTags($this->tags);

        $queue->delete();
    }
})->everyFiveMinutes();