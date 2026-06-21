<?php

declare(strict_types=1);

namespace App\Services;

use Generator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\StreamInterface;

class SimpleAskStreamService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
    }

    public function streamToOutput(
        array $messages,
        ?string $model = null,
        float $temperature = 1.0,
    ): string {
        $response = $this->sendStreamRequest($messages, $model, $temperature);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            logger('Stream request failed: ' . $error);
            logger('Status: ' . $response->status());
            echo "[ERROR] " . $error;
            $this->flush();
            return '';
        }

        logger('Stream request OK, status: ' . $response->status());

        $accumulated = '';

        foreach ($this->parseSSEStream($response->toPsrResponse()->getBody()) as $event) {
            if ($event['type'] === 'error') {
                echo "[ERROR] " . $event['data'];
                $this->flush();
                return $accumulated;
            }

            if ($event['type'] === 'content' && $event['data']) {
                $accumulated .= $event['data'];
                echo $event['data'];
                $this->flush();
            }
        }

        return $accumulated;
    }

    private function flush(): void
    {
        if (ob_get_level() > 0) ob_flush();
        flush();
    }

    private function sendStreamRequest(
        array $messages,
        ?string $model,
        float $temperature,
    ): \Illuminate\Http\Client\Response {
        return Http::withToken($this->apiKey)
            ->withHeaders([
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ])
            ->withOptions(['stream' => true])
            ->timeout(120)
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $model,
                'messages' => [$this->getSystemPrompt(), ...$messages],
                'temperature' => $temperature,
                'stream' => true,
            ]);
    }

    private function parseSSEStream(StreamInterface $body): Generator
    {
        $buffer = '';

        while (!$body->eof()) {
            $buffer .= $body->read(1024);

            while (($pos = strpos($buffer, "\n")) !== false) {
                $line = trim(substr($buffer, 0, $pos));
                $buffer = substr($buffer, $pos + 1);

                if ($event = $this->parseSSELine($line)) {
                    yield $event;
                }
            }
        }
    }

    private function parseSSELine(string $line): ?array
    {
        if ($line === '' || str_starts_with($line, ':')) return null;
        if (!str_starts_with($line, 'data: ')) return null;

        $data = substr($line, 6);
        if ($data === '[DONE]') return ['type' => 'done', 'data' => null];

        return $this->parseJSON($data);
    }

    private function parseJSON(string $json): ?array
    {
        try {
            $parsed = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (isset($parsed['error'])) {
                return ['type' => 'error', 'data' => $parsed['error']['message'] ?? 'Unknown error'];
            }

            $delta = $parsed['choices'][0]['delta'] ?? [];

            if (!empty($delta['content'])) {
                return ['type' => 'content', 'data' => $delta['content']];
            }

            return null;
        } catch (\JsonException) {
            return null;
        }
    }

    private function getSystemPrompt(): array
    {
        $user = Auth::user();
        $now = now()->locale('fr')->format('l d F Y H:i');

        return [
            'role' => 'system',
            'content' => view('prompts.system', [
                'now' => $now,
                'user' => $user,
            ])->render(),
        ];
    }
}
