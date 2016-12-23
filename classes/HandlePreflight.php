<?php

namespace OFFLINE\CORS\Classes;

use Closure;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;

class HandlePreflight
{
    /**
     * @param CorsService $cors
     */
    public function __construct(CorsService $cors, Router $router, Kernel $kernel)
    {
        $this->cors   = $cors;
        $this->router = $router;
        $this->kernel = $kernel;
    }

    /**
     * Handle an incoming OPTIONS request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if ($this->cors->isPreflightRequest($request) && $this->hasMatchingCorsRoute($request)) {
            $preflight = $this->cors->handlePreflightRequest($request);
            $response->headers->add($preflight->headers->all());
        }

        return $response;
    }

    /**
     * Verify the current OPTIONS request matches a CORS-enabled route
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return boolean
     */
    private function hasMatchingCorsRoute($request)
    {
        // Check if CORS is added in a global middleware
        if ($this->kernel->hasMiddleware(HandleCors::class)) {
            return true;
        }

        // Check if CORS is added as a route middleware
        $request = clone $request;
        $request->setMethod($request->header('Access-Control-Request-Method'));

        try {
            $route = $this->router->getRoutes()->match($request);
            // change of method name in laravel 5.3
            if (method_exists($this->router, 'gatherRouteMiddleware')) {
                $middleware = $this->router->gatherRouteMiddleware($route);
            } else {
                $middleware = $this->router->gatherRouteMiddlewares($route);
            }

            return in_array(HandleCors::class, $middleware);
        } catch (\Exception $e) {
            app('log')->error($e);

            return false;
        }
    }
}