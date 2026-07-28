<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $conversations = Conversation::with('user', 'messages')->latest()->get();

        return view('admin.messages.index', compact('conversations'));
    }

    public function show(Conversation $conversation)
    {
        $conversation->load('messages.user', 'user');
        $conversation->messages()->where('is_staff', false)->update(['read' => true]);

        return view('admin.messages.show', compact('conversation'));
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'is_staff' => true,
            'body' => $request->body,
        ]);

        return back();
    }
}
