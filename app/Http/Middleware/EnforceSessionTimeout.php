<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnforceSessionTimeout
{
    /**
     * Handle an incoming request and enforce server-side idle timeout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $timeoutMinutes = (int)SystemSetting::get('security.session_timeout_minutes', 30);

            if ($timeoutMinutes > 0 && $request->session()->has('last_activity_time')) {
                $lastActivity = (int)$request->session()->get('last_activity_time');
                $elapsedSeconds = time() - $lastActivity;

                if ($elapsedSeconds > ($timeoutMinutes * 60)) {
                    $user = Auth::user();
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    if ($request->expectsJson() || $request->is('api/*')) {
                        return response()->json([
                            'success'         => false,
                            'message'         => 'Your session has expired due to inactivity. Please log in again.',
                            'session_expired' => true,
                        ], 401);
                    }

                    return redirect()->guest('/login')->with('warning', 'Your session has expired due to inactivity. Please log in again.');
                }
            }

            $request->session()->put('last_activity_time', time());
        }

        return $next($request);
    }
}

