<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AskController extends Controller {
    public function __construct(private ChatService $chatService) {}

    public function index() {
        return Inertia::render('Ask/Index');
    }

    public function ask(Request $request) {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
        ]);

        $chat = Chat::create([
            'title' => '',
            'user_id' => Auth::id(),
            'favorite_ai' => $request->model,
        ]);

        try {
            $this->chatService->saveUserMessage($chat->id, $request->message);
            $history = $this->chatService->buildHistory($chat->id);
            $this->chatService->sendAndSave($chat->id, $history, $request->model);
            $title = $this->chatService->generateTitle($chat->id, $request->model);
            $chat->update(['title' => $title]);
        } catch (\Exception $e) {
            logger('AskController error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return redirect()->route('chat.show', $chat->id);
    }

    public function changeModel(Request $request) {
        $request->validate([
            'model' => 'required|string',
        ]);

        try {
            Auth::user()->update(['favorite_ai' => $request->model]);
        } catch (\Exception $e) {
            return back()->withErrors(['model' => $e->getMessage()]);
        }

        return back();
    }
}
