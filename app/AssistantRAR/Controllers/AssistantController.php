<?php

namespace App\AssistantRAR\Controllers;

use App\AssistantRAR\Contracts\IAssistantService;
use App\AssistantRAR\Contracts\IConversationService;
use App\AssistantRAR\Contracts\IMemoryService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AssistantController extends Controller
{
    public function __construct(
        private readonly IAssistantService $assistant,
        private readonly IConversationService $conversation,
        private readonly IMemoryService $memory,
    ) {}

    public function conversations()
    {
        $conversations = $this->conversation->list(auth()->id());
        return response()->json($conversations);
    }

    public function createConversation(Request $request)
    {
        $request->validate(['title' => 'nullable|string|max:255']);
        $conversation = $this->conversation->create(auth()->id(), $request->title);
        return response()->json($conversation, 201);
    }

    public function getConversation(int $id)
    {
        $data = $this->conversation->get($id, auth()->id());
        return response()->json($data);
    }

    public function deleteConversation(int $id)
    {
        $this->conversation->delete($id, auth()->id());
        return response()->json(['success' => true]);
    }

    public function sendMessage(Request $request, int $conversationId)
    {
        $request->validate(['message' => 'required|string|max:4000']);

        $result = $this->assistant->processMessage(
            auth()->id(),
            $conversationId,
            $request->message,
        );

        return response()->json($result);
    }

    public function streamMessage(Request $request, int $conversationId)
    {
        $request->validate(['message' => 'required|string|max:4000']);

        return response()->stream(function () use ($conversationId, $request) {
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('X-Accel-Buffering: no');

            $this->assistant->processStream(
                auth()->id(),
                $conversationId,
                $request->message,
                function ($chunk) {
                    echo "data: " . json_encode(['content' => $chunk]) . "\n\n";
                    ob_flush();
                    flush();
                },
            );

            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function memories()
    {
        return response()->json($this->memory->getAll(auth()->id()));
    }

    public function saveMemory(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:100',
            'value' => 'required|string|max:2000',
            'category' => 'nullable|string|max:50',
        ]);

        $this->memory->set(auth()->id(), $request->key, $request->value, $request->category);
        return response()->json(['success' => true]);
    }

    public function deleteMemory(string $key)
    {
        $this->memory->delete(auth()->id(), $key);
        return response()->json(['success' => true]);
    }
}
