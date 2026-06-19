<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatController extends Controller
{
    public function __construct() {}

    public function index(string $chatID) {
        $chat = Chat::where('user_id', Auth::id())
                ->where('id', $chatID)
                ->with('messages')
                ->firstOrFail();

        return Inertia::render("Chat/Index", [
            'chat' => $chat,
            'messages' => $chat->messages,
        ]);
    }
}
