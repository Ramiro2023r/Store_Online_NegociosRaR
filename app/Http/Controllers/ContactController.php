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

    public function messages()
    {
        $conversation = Conversation::where('user_id', Auth::id())->where('status', 'abierta')->first();
        if (!$conversation) {
            return response()->json([]);
        }

        $afterId = request('after_id', 0);
        $msgs = $conversation->messages()
            ->where('id', '>', $afterId)
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'is_staff' => $m->is_staff,
                'user_name' => $m->is_staff ? 'Soporte Negocios RaR' : 'Tú',
                'created_at' => $m->created_at->diffForHumans(),
            ]);

        $conversation->messages()->where('is_staff', true)->where('read', false)->update(['read' => true]);

        return response()->json($msgs);
    }

    public function send(Request $request)
    {
        $request->validate(['body' => 'required|string|max:2000']);

        $conversation = Conversation::firstOrCreate(
            ['user_id' => Auth::id(), 'status' => 'abierta'],
            ['subject' => 'Consulta general']
        );

        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'is_staff' => false,
            'body' => $request->body,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['id' => $msg->id]);
        }

        return back();
    }
}
