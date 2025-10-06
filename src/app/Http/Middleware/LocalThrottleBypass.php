<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests as CoreThrottle;

class LocalThrottleBypass
{
    public function __construct(private CoreThrottle $throttle) {}

    public function handle(Request $request, Closure $next, ...$params)
    {
        if (app()->environment(['local', 'testing'])) {
            return $next($request);
        }
        return $this->throttle->handle($request, $next, ...$params);
    }
}
