<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BasicHttpAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authIsEnabled = env('BASIC_AUTH_ENABLED');
        if (!$authIsEnabled){
            return $next($request);
        }

        $ENV_USERNAME = env('BASIC_AUTH_USERNAME');
        $ENV_PASSWORD = env('BASIC_AUTH_PASSWORD');

        if (!$ENV_USERNAME || !$ENV_PASSWORD) {
            $headers = ['WWW-Authenticate' => 'Basic'];
            return response('Unauthorized', 401, $headers);
        }

        if ($request->getUser() !== $ENV_USERNAME || $request->getPassword() !== $ENV_PASSWORD) {
            $headers = ['WWW-Authenticate' => 'Basic'];

            // If the username or password is wrong, send the headers to prompt the user again.
            return response('Unauthorized', 401, $headers);
        }

        // If the user is authenticated, pass the request to the next middleware.
        return $next($request);
    }
}
