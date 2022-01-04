<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Middleware;

class ServiceExecutorMiddleware implements MiddlewareInterface
{
    public function execute($proxy, $instance, $method, $params, &$returnEarly, object $decoratedService, callable $next)
    {
        $returnEarly = true;

        return call_user_func_array([$decoratedService, $method], $params);
    }
}
