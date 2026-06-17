<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SimpleAskService;
use Inertia\Inertia;

class AskController extends Controller {
    public function __construct(private SimpleAskService $askService) {}

    public function index() {
        return Inertia::render('Ask/Index', [
            'models' => $this->askService->getModels(),
            'selectedModel' => $this->askService::DEFAULT_MODEL,
        ]);
    }

    public function ask(Request $request) {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
        ]);

        $response = null;
        $error = null;
        $message = [[
            'role' => 'user',
            'content' => $request->message,
        ]];

        try {
            $response = $this->askService->sendMessage(
                messages: $message,
                model: $request->model
            );
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        return Inertia::render('Ask/Index', [
            'models' => $this->askService->getModels(),
            'selectedModel' => $request->model,
            'message' => $request->message,
            'response' => $response,
            'error' => $error,
        ]);
    }
}
