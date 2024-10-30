<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;


class PostController extends Controller
{
    public function show(int $id)
    {
        $post = Post::find($id);

        if ($post == null || $post->hidden)
            return redirect()->route('dashboard');

        return view('post.show', [
            'post' => $post
        ]);
    }
}
