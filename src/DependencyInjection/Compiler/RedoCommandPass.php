<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Command\RedoCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RedoCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(RedoCommand::class, $container);
    }
}
