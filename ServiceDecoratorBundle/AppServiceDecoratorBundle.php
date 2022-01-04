<?php

declare(strict_types=1);

namespace App\ServiceDecoratorBundle;

use App\ServiceDecoratorBundle\DependencyInjection\Compiler\ServiceDecoratorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AppServiceDecoratorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ServiceDecoratorCompilerPass());
    }
}
