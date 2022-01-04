<?php

declare(strict_types=1);

namespace App\ServiceDecorator\Attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
class EventDispatching implements MethodDecoratorInterface
{
}
