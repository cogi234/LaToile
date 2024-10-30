<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    public function show(int $id)
    {
        $post = Post::find($id);

        //If the post doesn't exist, or it's hidden and the user isn't connected or a moderator
        if ($post == null || ($post->hidden && !Auth::check() && !Auth::user()->moderator))
            return redirect()->route('dashboard');

        return view('post.show', [
            'post' => $post
        ]);
    }
}
