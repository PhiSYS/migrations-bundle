<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Command\StatusCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class StatusCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(StatusCommand::class, $container);
    }
}
