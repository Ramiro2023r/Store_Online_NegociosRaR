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

    public function messages(Conversation $conversation)
    {
        $afterId = request('after_id', 0);
        $msgs = $conversation->messages()
            ->where('id', '>', $afterId)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'is_staff' => $m->is_staff,
                'user_name' => $m->is_staff ? 'Soporte' : $conversation->user->name,
                'created_at' => $m->created_at->diffForHumans(),
            ]);

        $conversation->messages()->where('is_staff', false)->where('read', false)->update(['read' => true]);

        return response()->json($msgs);
    }

    public function reply(Request $request, Conversation $conversation)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'is_staff' => true,
            'body' => $request->body,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $msg->id]);
        }

        return back();
    }
}
