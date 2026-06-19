<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use App\Services\SimpleAskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct(private SimpleAskService $askService) {}

    public function index(string $chatId) {
        $chat = Chat::where('user_id', Auth::id())
                ->where('id', $chatId)
                ->with('messages')
                ->firstOrFail();

        return Inertia::render("Chat/Index", [
            'chat' => $chat,
            'messages' => $chat->messages()->oldest()->get(),
        ]);
    }

    public function ask(Request $request, string $chatId) {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
        ]);

        $response = null;
        $question = [[
            'role' => 'user',
            'content' => $request->message,
        ]];

        Message::create([
            'content' => $request->message,
            'chat_id' => $chatId,
            'role' => 'user',
        ]);

        $history = Message::where('chat_id', $chatId)
            ->oldest()
            ->get()
            ->map(fn($m) => [
                'role' => $m->role === 'LLM' ? 'assistant' : 'user',
                'content' => $m->content,
            ])
            ->toArray();

        try {
            $response = $this->askService->sendMessage(
                messages: $history,
                model: $request->model,
            );

            Message::create([
                'content' => $response,
                'chat_id' => $chatId,
                'role' => 'LLM',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back();
    }
}
