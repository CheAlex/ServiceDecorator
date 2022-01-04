<?php

declare(strict_types=1);

namespace App\ServiceDecorator;

use App\ServiceDecorator\Exception\InvalidMiddlewareException;
use App\ServiceDecorator\Middleware\MiddlewareInterface;
use App\ServiceDecorator\Middleware\MiddlewareLocator;
use App\ServiceDecorator\Middleware\ServiceExecutorMiddleware;
use ProxyManager\Factory\AccessInterceptorValueHolderFactory;

class ServiceDecoratorFactory
{
    public function __construct(
        private AccessInterceptorValueHolderFactory $proxyFactory,
        private MiddlewareLocator $middlewareLocator,
        private ServiceExecutorMiddleware $serviceExecutorMiddleware
    ) {
    }

    /**
     * @param array<string, array<string>> $methodToAttributeListMap
     */
    public function create(object $decoratedService, array $methodToAttributeListMap): object
    {
        $proxy = $this->proxyFactory->createProxy($decoratedService);

        foreach ($methodToAttributeListMap as $method => $attributeList) {
            $middlewareList = $this->middlewareLocator->getAllByAttributeList($attributeList);
            $middlewareChain = $this->createExecutionChain($middlewareList);
            $prefixInterceptor = static function ($proxy, $instance, $method, $params, &$returnEarly) use ($middlewareChain, $decoratedService) {
                return $middlewareChain($proxy, $instance, $method, $params, $returnEarly, $decoratedService);
            };

            $proxy->setMethodPrefixInterceptor($method, $prefixInterceptor);
        }

        return $proxy;
    }

    /**
     * @param MiddlewareInterface[] $middlewareList
     *
     * @return callable
     */
    private function createExecutionChain($middlewareList)
    {
        $lastCallable = static function () {
            // the final callable is a no-op
        };

        $resultMiddlewareList = $middlewareList;
        $resultMiddlewareList[] = $this->serviceExecutorMiddleware;

        while ($middleware = array_pop($resultMiddlewareList)) {
            if (! $middleware instanceof MiddlewareInterface) {
                throw InvalidMiddlewareException::forMiddleware($middleware);
            }

            $lastCallable = static function ($proxy, $instance, $method, $params, &$returnEarly, object $decoratedService) use ($middleware, $lastCallable) {
                return $middleware->execute($proxy, $instance, $method, $params, $returnEarly, $decoratedService, $lastCallable);
            };
        }

        return $lastCallable;
    }
}
