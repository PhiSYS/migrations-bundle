<?php
declare(strict_types=1);

namespace PhiSYS\MigrationsBundle\DependencyInjection\Compiler;

use PhiSYS\MigrationsBundle\Command\DownCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DownCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(DownCommand::class, $container);
    }
}
