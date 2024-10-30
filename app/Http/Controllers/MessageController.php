<?php

namespace App\Http\Controllers;
use App\Models\PrivateMessage;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show()
    {
        return view('messages.show', [
            'targetUserId' => null,
        ]);
    }
    public function showUserConversation($targetId = null)
    {
        return view('messages.show', [
            'targetUserId' => $targetId,
        ]);
    }

    public function showGroupConversation($targetGroupId = null)
    {
        return view('messages.show', [
            'targetGroupId' => $targetGroupId,
        ]);
    }
}
