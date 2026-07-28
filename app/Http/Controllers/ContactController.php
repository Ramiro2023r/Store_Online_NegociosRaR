<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    public function index()
    {
        $conversation = Conversation::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'abierta'],
            ['subject' => 'Consulta general']
        );
        $conversation->load('messages.user');

        return view('contact', compact('conversation'));
    }

    public function send(Request $request)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $conversation = Conversation::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'abierta'],
            ['subject' => 'Consulta general']
        );

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'is_staff' => false,
            'body' => $request->body,
        ]);

        return back();
    }
}
