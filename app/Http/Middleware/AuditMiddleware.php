<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        AuditLog::create([
            'user'        => auth()->check() ? auth()->user()->email : 'guest',
            'method'      => $request->method(),
            'url'         => $request->fullUrl(),
            'ip'          => $request->ip(),
            'payload'     => json_encode($request->all()),
            'status_code' => $response->getStatusCode()
        ]);

        return $response;
    }
}
