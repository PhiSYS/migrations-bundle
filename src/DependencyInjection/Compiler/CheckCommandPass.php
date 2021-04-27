<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Command\CheckCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CheckCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(CheckCommand::class, $container);
    }
}
