<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
    public static function getAccessibleRoutes($role)
    {
        $accessibleRoutes = [];
        $routes = Route::getRoutes();

        foreach ($routes as $route) {
            $actionMiddleware = $route->gatherMiddleware();

            foreach ($actionMiddleware as $middleware) {
                if (strpos($middleware, 'RoleMiddleware') !== false) {
                    // Ambil role yang diizinkan dari middleware
                    preg_match('/RoleMiddleware:(.+)/', $middleware, $matches);
                    $roles = isset($matches[1]) ? explode(',', $matches[1]) : [];

                    // Periksa apakah role cocok
                    if (in_array($role, $roles)) {
                        $accessibleRoutes[] = $route->uri();
                    }
                }
            }
        }

        return $accessibleRoutes;
    }
}
