<?php

namespace App\Http\Middleware;

use Closure;
use Spatie\Permission\Models\Permission;

class ClearanceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (tvHasRole(auth('web')->user()->id, 'Root') || tvHasRole(auth('web')->user()->id, 'root')) {
            return $next($request);
        } else {
            $roleIdArray = \App\ModelHasRole::where('model_id', auth('web')->user()->id)->pluck('role_id');
            $permissionArray = \App\RoleHasPermission::whereIn('role_id', $roleIdArray)->pluck('permission_id');

            $permissions = Permission::select('url')
                ->where('method', $request->method())
                ->whereIn('id', $permissionArray)
                ->get()->toArray();
            foreach ($permissions as $permission) {
                if ($request->is($permission['url'])) {
                    return $next($request);
                }
            }
        }
        abort('401');
    }
}
