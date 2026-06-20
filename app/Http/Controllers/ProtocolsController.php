<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProtocolsController extends Controller
{
    public function __construct() {}

    public function index() {
        return Inertia::render('Protocols/Index', [
            'user' => Auth::user(),
        ]);
    }

    public function save(Request $request) {
        $request->validate([
            'identity' => 'nullable|array',
            'tone' => 'nullable|array',
            'commands' => 'nullable|array',
        ]);

        Auth::user()->update([
            'identity' => $request->identity,
            'tone' => $request->tone,
            'commands' => $request->commands,
        ]);

        return back();
    }
}
