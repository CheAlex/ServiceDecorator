<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Middleware;

use Psr\Container\ContainerInterface;

class MiddlewareLocator
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    /**
     * @param string[] $attributeList
     * @return MiddlewareInterface[]
     */
    public function getAllByAttributeList(array $attributeList): array
    {
        $middlewareList = [];

        foreach ($attributeList as $attribute) {
            $middlewareList[] = $this->container->get($attribute);
        }

        return $middlewareList;
    }
}
