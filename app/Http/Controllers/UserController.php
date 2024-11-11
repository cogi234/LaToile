<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function show(int $id)
    {
        return view('user.profile',[
            'user' => User::findOrFail($id)
        ]);
    }

    public function showFollowers(int $id)
    {
        return view('user.follow',[
            'user' => User::findOrFail($id),
            'viewFollowers' => true
        ]);
    }

    public function showFollowings(int $id)
    {
        return view('user.follow',[
            'user' => User::findOrFail($id),
            'viewFollowers' => false
        ]);
    }
}
