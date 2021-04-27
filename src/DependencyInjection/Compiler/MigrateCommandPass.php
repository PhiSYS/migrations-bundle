<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Command\MigrateCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MigrateCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(MigrateCommand::class, $container);
    }
}
