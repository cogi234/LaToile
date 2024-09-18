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
}
