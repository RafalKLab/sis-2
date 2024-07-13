<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // Retrieve the authenticated user
        $user = Auth::user();
        // Check if the user is blocked
        if ($user->is_blocked) {
            // Log out the user immediately if they are blocked
            Auth::guard('web')->logout();

            // Invalidate the session
            $request->session()->invalidate();

            // Regenerate the session token
            $request->session()->regenerateToken();

            // Redirect back to the login page with an error message
            return back()->withErrors([
                'email' => 'Your account has been blocked.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
