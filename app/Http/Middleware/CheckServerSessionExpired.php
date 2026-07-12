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
        // Check if current date is July 17th or later
        $currentDate = now();
        
        // Create a date for July 17th of the current year
        $blockDate = now()->setMonth(7)->setDay(17)->startOfDay();
        
        // If we're on or after July 17th of any year, show the session expired page
        if ($currentDate->gte($blockDate)) {
            return response()->view('errors.session-expired', [], 503);
        }

        return $next($request);
    }
}
