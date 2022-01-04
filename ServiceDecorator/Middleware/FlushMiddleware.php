<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Middleware;

use Doctrine\ORM\EntityManagerInterface;

class FlushMiddleware implements MiddlewareInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    public function execute($proxy, $instance, $method, $params, &$returnEarly, object $decoratedService, callable $next)
    {
        echo 'flush:start;';
        $returnValue = $next($proxy, $instance, $method, $params, $returnEarly, $decoratedService);
        echo 'flush:stop;';

        return $returnValue;

    }
}
