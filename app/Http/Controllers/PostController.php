<?php

namespace App\Http\Controllers;

use App\Models\Post;


class PostController extends Controller
{
    public function show(int $id)
    {
        return view('post.show',[
            'post' => Post::findOrFail($id)
        ]);
    }
}
