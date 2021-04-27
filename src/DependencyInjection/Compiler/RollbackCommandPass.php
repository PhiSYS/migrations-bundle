<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Command\RollbackCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RollbackCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(RollbackCommand::class, $container);
    }
}
