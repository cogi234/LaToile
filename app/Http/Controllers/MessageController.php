<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\PrivateMessage;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function show($currentId = null, $targetId = null)
    {


        // $currentId = $currentId ?? Auth::id();

        // if (Auth::id() != $currentId) {
        //     abort(403);
        // }

        $privateMessages = PrivateMessage::where('sender_id', $currentId)
            ->orWhere('receiver_id', $currentId)
            ->get();

        $selectedConversation = [];
        if ($targetId) {
            $selectedConversation = PrivateMessage::where(function($query) use ($currentId, $targetId) {
                $query->where('sender_id', $currentId)
                      ->where('receiver_id', $targetId);
            })->orWhere(function($query) use ($currentId, $targetId) {
                $query->where('sender_id', $targetId)
                      ->where('receiver_id', $currentId);
            })->get();
        }

        return view('messages.messageBoard', [
            'privateMessages' => $privateMessages,
            'selectedConversation' => $selectedConversation,
            'targetUserId' => $targetId
        ]);
    }
}
