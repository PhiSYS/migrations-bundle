<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Infrastructure\Service\Phpmig\ConfigurationContainer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

abstract class AbstractCommandPass implements CompilerPassInterface
{
    protected function addCommandDefinition(string $commandClass, ContainerBuilder $container): void
    {
        $commandDefinition = new Definition(
            $commandClass,
            [
                new Reference(ConfigurationContainer::class),
            ],
        );

        $container->addDefinitions(
            [
                $commandClass => $commandDefinition->addTag('console.command'),
            ],
        );
    }
}
