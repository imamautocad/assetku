<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermissionGroup
{
    public function handle(Request $request, Closure $next, $permissionKey)
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (!method_exists($user, 'permissionGroup')) {
            abort(403, 'No permission group relation found');
        }

        $role = $user->permissionGroup;

        if (!$role) {
            abort(403, 'User has no permission group');
        }

        $permissions = json_decode($role->permissions, true);

        if (!isset($permissions[$permissionKey]) || $permissions[$permissionKey] != 1) {
            abort(403, "You do not have permission: $permissionKey");
        }

        return $next($request);
    }
}
