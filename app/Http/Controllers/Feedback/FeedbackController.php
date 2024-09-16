<?php

namespace App\Http\Controllers\Feedback;

use App\Models\Dashboard\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController
{
    public function create(Request $request) {
        $validated = $request->validate([
            'feedback' => 'required',
        ]);

        $user = Auth::user();

        Feedback::create([
            'user' => sprintf('%s (%s)', $user->name, $user->email),
            'message' => $validated['feedback']
        ]);

        return redirect()->route('dashboard');
    }
}
