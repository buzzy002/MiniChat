<?php

namespace App\Services;

use App\Models\Message;

class ChatService
{
    /**
     * Create a new class instance.
     */
    public function __construct(private SimpleAskService $askService) {}

    public function saveUserMessage(string $chatId, string $content) {
        return Message::create([
            'content' => $content,
            'chat_id' => $chatId,
            'role' => 'user',
        ]);
    }

    public function buildHistory(string $chatId) {
        return Message::where('chat_id', $chatId)
            ->oldest()
            ->get()
            ->map(fn($message) => [
                'role' => $message->role === 'LLM' ? 'assistant' : 'user',
                'content' => $message->content,
            ])
            ->toArray();
    }

    public function sendAndSave(string $chatId, array $history, string $model) {
        $response = $this->askService->sendMessage(
            messages: $history,
            model: $model,
        );

        Message::create([
            'content' => $response,
            'chat_id' => $chatId,
            'role' => 'LLM',
        ]);
    }

    public function generateTitle(string $chatId, string $model) {
        $history = $this->buildHistory($chatId);

        return trim($this->askService->sendMessage(
            messages: $history,
            model: $model,
            systemPrompt: 'Generate a short title (max 5 words) for this conversation. Return only the title, no punctuation, no explanation.'
        ));
    }
}
