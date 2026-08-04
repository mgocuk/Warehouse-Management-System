<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !$request->user()->is_active) {
            auth()->logout();

            // Return JSON 401 for AJAX/API requests
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Oturumunuz sonlandı veya hesabınız aktif değil.'], 401);
            }

            return redirect()->route('login')->with('error', 'Hesabınız aktif değil veya oturumunuz sonlandı.');
        }

        // Use mb_strtolower for correct Turkish multi-byte character handling
        $userRole = mb_strtolower($request->user()->role->name ?? '', 'UTF-8');

        // Admin bypasses all role checks
        if ($userRole === 'admin') {
            return $next($request);
        }

        foreach ($roles as $role) {
            $normalizedRole = mb_strtolower(trim($role), 'UTF-8');

            // Direct role match
            if ($userRole === $normalizedRole) {
                return $next($request);
            }

            // 'depo sorumlusu' (manager) can access 'görüntüleme' (viewer) routes as well
            if ($normalizedRole === 'görüntüleme' && $userRole === 'depo sorumlusu') {
                return $next($request);
            }
        }

        abort(403, 'Bu işlemi yapmak için yetkiniz bulunmamaktadır.');
    }
}
