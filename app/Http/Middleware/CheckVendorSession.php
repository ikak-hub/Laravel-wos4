<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('vendor_id')) {
            return redirect()->route('kantor.login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }
        return $next($request);
    }
}