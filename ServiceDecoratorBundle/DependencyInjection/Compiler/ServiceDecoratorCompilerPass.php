<?php

declare(strict_types=1);

namespace App\ServiceDecoratorBundle\DependencyInjection\Compiler;

use App\ServiceDecorator\Attribute\MethodDecoratorInterface;
use App\ServiceDecorator\ServiceDecoratorFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ServiceDecoratorCompilerPass implements CompilerPassInterface
{
    private const DECORATOR_SERVICE_SUFFIX = '.decorator';

    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            try {
                if (empty($definition->getClass()) || !class_exists($definition->getClass())) {
                    continue;
                }
            } catch (\Error $e) {
                if (preg_match('/^(Class|Interface|Trait) [\'"]([^\'"]+)[\'"] not found$/', $e->getMessage())) {
                    continue;
                }
            }

            $methodToAttributeListMap = $this->fetchMethodToAttributeListMap($definition->getClass());

            if (0 === count($methodToAttributeListMap)) {
                continue;
            }

            $container->register($id . self::DECORATOR_SERVICE_SUFFIX, $definition->getClass())
                ->setDecoratedService($id)
                ->setFactory([new Reference(ServiceDecoratorFactory::class), 'create'])
                ->setArguments([new Reference('.inner'), $methodToAttributeListMap])
            ;
        }
    }

    private function fetchMethodToAttributeListMap(string $serviceClass): array
    {
        $reflectionMethodList = (new \ReflectionClass($serviceClass))->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methodToAttributeListMap = [];

        foreach ($reflectionMethodList as $reflectionMethod) {
            foreach ($reflectionMethod->getAttributes() as $reflectionAttribute) {
                if (!is_subclass_of($reflectionAttribute->getName(), MethodDecoratorInterface::class)) {
                    continue;
                }

                $methodToAttributeListMap[$reflectionMethod->getName()][] = $reflectionAttribute->getName();
            }
        }

        return $methodToAttributeListMap;
    }
}
