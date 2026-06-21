<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\ChatService;
use App\Services\SimpleAskStreamService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct(private ChatService $chatService, private SimpleAskStreamService $streamService) {}

    public function show(string $chatId) {
        $chat = Chat::where('user_id', Auth::id())
            ->where('id', $chatId)
            ->with('messages')
            ->firstOrFail();

        return Inertia::render("Chat/Index", [
            'chat' => $chat,
            'messages' => $chat->messages()->oldest()->get(),
            'pending' => $chat->messages()->latest()->first()->role === 'user',
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
        } catch (\Exception $e) {
            logger('ChatController error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('chat.show', ['chatId' => $chatId]);
    }

    public function stream(Request $request, string $chatId) {
        $request->validate(['model' => 'required|string']);

        $history = $this->chatService->buildHistory($chatId);

        return response()->stream(
            function () use ($history, $chatId, $request): void {
                $accumulated = $this->streamService->streamToOutput(
                    messages: $history,
                    model: $request->model,
                );

                logger('accumulated length: ' . strlen($accumulated));

                $this->chatService->saveLLMMessage($chatId, $accumulated);

                $chat = Chat::where('user_id', Auth::id())
                    ->where('id', $chatId)
                    ->first();
                logger('chat title: ' . $chat->title);
                if ($chat && $chat->title === '') {
                    $title = $this->chatService->generateTitle($chatId, $request->model);
                    $chat->update(['title' => $title]);
                }
            },
            headers: [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-cache, no-store',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }
}
