<?php

namespace Nrauf90\LaravelAcl\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class CheckAclPermission
{
    public function handle($request, Closure $next)
    {
        $controllerAction = class_basename(Route::current()->getActionName());
        [$controller, $method] = explode('@', $controllerAction);

        if (auth()->check() && auth()->user()->hasPermission($controller, $method)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
