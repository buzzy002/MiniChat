<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService) {}

    public function show(string $chatId) {
        $chat = Chat::where('user_id', Auth::id())
            ->where('id', $chatId)
            ->with('messages')
            ->firstOrFail();

        return Inertia::render("Chat/Index", [
            'chat' => $chat,
            'messages' => $chat->messages()->oldest()->get(),
        ]);
    }

    public function destroy(string $chatId) {
        $chat = Chat::where('user_id', Auth::id())
            ->where('id', $chatId)
            ->firstOrFail();
        $chat->delete();
        return redirect()->route('ask.index');
    }

    public function ask(Request $request, string $chatId) {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
        ]);

        try {
            $this->chatService->saveUserMessage($chatId, $request->message);
            $history = $this->chatService->buildHistory($chatId);
            $this->chatService->sendAndSave($chatId, $history, $request->model);
        } catch (\Exception $e) {
            logger('ChatController error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back();
    }
}
