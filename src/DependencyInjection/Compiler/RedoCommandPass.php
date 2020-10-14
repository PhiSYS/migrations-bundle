<?php
declare(strict_types=1);

namespace DosFarma\MigrationsBundle\DependencyInjection\Compiler;

use DosFarma\MigrationsBundle\Command\RedoCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RedoCommandPass extends AbstractCommandPass
{
    public function process(ContainerBuilder $container): void
    {
        $this->addCommandDefinition(RedoCommand::class, $container);
    }
}
