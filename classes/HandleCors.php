<?php

namespace OFFLINE\CORS\Classes;

use Closure;

class HandleCors
{
    /**
     * The CORS service
     *
     * @var CorsService
     */
    protected $cors;

    /**
     * @param CorsService $cors
     */
    public function __construct(CorsService $cors)
    {
        $this->cors = $cors;
    }

    /**
     * Handle an incoming request. Based on Asm89\Stack\Cors by asm89
     * @see https://github.com/asm89/stack-cors/blob/master/src/Asm89/Stack/Cors.php
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( ! $this->cors->isCorsRequest($request)) {
            return $next($request);
        }

        if ( ! $this->cors->isActualRequestAllowed($request)) {
            abort(403);
        }

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        return $this->cors->addActualRequestHeaders($response, $request);
    }

}