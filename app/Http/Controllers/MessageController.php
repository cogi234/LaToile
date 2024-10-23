<?php

namespace App\Http\Controllers;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show($targetId = null)
    {
        return view('messages.show', [
            'targetUserId' => $targetId
        ]);
    }
    


}
