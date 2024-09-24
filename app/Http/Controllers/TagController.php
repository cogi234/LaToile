<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function show(int $id)
    {
        return view('tag.show',[
            'tag' => Tag::findOrFail($id)
        ]);
    }
}
