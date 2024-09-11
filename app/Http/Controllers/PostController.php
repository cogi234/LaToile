<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use View;

class PostController extends Controller
{
    public function show(int $id)
    {
        return view('post.show',[
            'post' => Post::findOrFail($id)
        ]);
    }
}
