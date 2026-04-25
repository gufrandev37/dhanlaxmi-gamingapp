<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $admin = auth()->guard('admin')->user();

        // 1️⃣ Not logged in
        if (!$admin) {
            abort(403, 'Unauthorized');
        }

        // 2️⃣ Super Admin → full access
        if ($admin->isSuperAdmin()) {
            return $next($request);
        }

        // 3️⃣ Permission check for normal roles
        if (!$admin->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
