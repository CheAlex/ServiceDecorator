<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Middleware;

interface MiddlewareInterface
{
    /**
     * @var object $proxy       the proxy that intercepted the method call
     * @var object $instance    the decorated instance within the proxy
     * @var string $method      name of the called method
     * @var array  $params      sorted array of parameters passed to the intercepted
     *                          method, indexed by parameter name
     * @var bool   $returnEarly flag to tell the interceptor proxy to return early, returning
     *                          the interceptor's return value instead of executing the method logic
     *
     * @return mixed
     */
    public function execute($proxy, $instance, $method, $params, &$returnEarly, object $decoratedService, callable $next);
}
