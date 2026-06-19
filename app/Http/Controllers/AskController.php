<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\SimpleAskService;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AskController extends Controller {
    public function __construct(private SimpleAskService $askService) {}

    public function index() {
        return Inertia::render('Ask/Index');
    }

    public function ask(Request $request) {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
        ]);

        $response = null;
        $error = null;
        $question = [[
            'role' => 'user',
            'content' => $request->message,
        ]];

        $chat = Chat::create([
            'title' => 'test',
            'user_id' => Auth::id(),
            'favorite_ai' => $request->model,
        ]);

        Message::create([
            'content' => $request->message,
            'chat_id' => $chat->id,
            'role' => 'user',
        ]);

        try {
            $response = $this->askService->sendMessage(
                messages: $question,
                model: $request->model
            );

            Message::create([
                'content' => $response,
                'chat_id' => $chat->id,
                'role' => 'LLM',
            ]);

            $title = $this->askService->sendMessage(
                messages: [
                    ['role' => 'user', 'content' => $request->message],
                    ['role' => 'assistant', 'content' => $response],
                ],
                model: $request->model,
                systemPrompt: 'Generate a short title (max 5 words) for this conversation. Return only the title, no punctuation, no explanation.'
            );
            $chat->update(['title' => trim($title)]);
        } catch (\Exception $e) {
            logger('AskController error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        $chat = Chat::where('user_id', Auth::id())
            ->where('id', $chat->id)
            ->with('messages')
            ->firstOrFail();

        return redirect()->route('chat.index', $chat->id);
    }

    public function changeModel(Request $request) {
        $request->validate([
            'model' => 'required|string',
        ]);

        try {
            auth()->user()->update(['favorite_ai' => $request->model]);
        } catch (\Exception $e) {
            return back()->withErrors(['model' => $e->getMessage()]);
        }

        return back();
    }
}
